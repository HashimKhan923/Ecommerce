<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Models\CampaignSegment;
use App\Models\LinkStat;
use App\Models\Segment;
use App\Models\Subscriber;
use App\Models\User;
use App\Jobs\SendCampaignEmail;
use Carbon\Carbon;

class CampaignController extends Controller
{
    public function index() {


        
        $campaigns = Campaign::with('segments','trackingEvents','opens','clicks')->withCount('recipients')->where('seller_id', null)->get();

        return response()->json(['campaigns'=>$campaigns]);
    }

    public function detail($id) {

        $campaign = Campaign::with('segments.segment','trackingEvents','opens','clicks')->withCount('recipients')->where('id',$id)->first();;

        $links = LinkStat::where('campaign_id', $id)->get();


        return response()->json(['campaign'=>$campaign,'links'=>$links]);
    }

    public function show($id)
    {
        $campaign = Campaign::with('summary')->findOrFail($id);

        if ($campaign->summary) {
            // Use rolled up stats
            return response()->json([
                'total_sent' => $campaign->summary->total_sent,
                'total_opened' => $campaign->summary->total_opened,
                'total_clicked' => $campaign->summary->total_clicked,
                'open_rate' => $campaign->summary->open_rate,
                'click_rate' => $campaign->summary->click_rate,
            ]);
        } else {
        // Total sent (users + subscribers)
        $totalSent = $campaign->allRecipients()->count();

        // Opens (users + subscribers)
        $totalOpened = $campaign->trackingEvents()
            ->where('event_type', 'open')
            ->count();

        // Clicks (users + subscribers)
        $totalClicked = $campaign->trackingEvents()
            ->where('event_type', 'click')
            ->count();

        // Unsubscribed (users + subscribers)
        $totalUnsubscribed = $campaign->allRecipients()
            ->where('unsubscribed', true)
            ->count();

        $openRate = $totalSent > 0 ? round(($totalOpened / $totalSent) * 100, 2) : 0;
        $clickRate = $totalSent > 0 ? round(($totalClicked / $totalSent) * 100, 2) : 0;

        return response()->json([
            'total_sent'        => $totalSent,
            'total_opened'      => $totalOpened,
            'total_clicked'     => $totalClicked,
            'total_unsubscribed'=> $totalUnsubscribed,
            'open_rate'         => $openRate,
            'click_rate'        => $clickRate,
        ]);
                }
    }


public function create(Request $request)
{
    $campaign = Campaign::create([
        'seller_id'     => null,
        'name'          => $request->name,
        'subject'       => $request->subject,
        'preview_text'  => $request->preview_text,
        'content'       => $request->content,
        'send_time'     => $request->send_time,
        'status'        => $request->status,
    ]);

    $allMatchedUsers = collect();
    $allMatchedSubscribers = collect();

    // Load once for performance
    $allUsers = User::with('order')->get();
    $allSubscribers = Subscriber::all();

    foreach ($request->segment_ids as $segment_id) {
        CampaignSegment::create([
            'campaign_id' => $campaign->id,
            'segment_id'  => $segment_id,
        ]);

        $segment = Segment::findOrFail($segment_id);

        // Decide which base collection to filter
        if ($segment->segment_type === 'subscriber') {

            $matched = $allSubscribers->filter(function ($subscriber) use ($segment) {
                // evaluateRules must support Subscriber entity as well
                return $this->evaluateRules($subscriber, $segment->rules);
            });

            $allMatchedSubscribers = $allMatchedSubscribers->merge($matched);

        } else {
            // default: users
            $matched = $allUsers->filter(function ($user) use ($segment) {
                return $this->evaluateRules($user, $segment->rules);
            });

            $allMatchedUsers = $allMatchedUsers->merge($matched);
        }
    }

    // Unique IDs
    $userIds = $allMatchedUsers
        ->pluck('id')
        ->filter()
        ->unique()
        ->values();

    $subscriberIds = $allMatchedSubscribers
        ->pluck('id')
        ->filter()
        ->unique()
        ->values();

    // Build recipient rows for users
    $recipients = collect();

    foreach ($userIds as $userId) {
        $recipients->push([
            'campaign_id'   => $campaign->id,
            'user_id'       => $userId,
            'subscriber_id' => null,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    // Build recipient rows for subscribers
    foreach ($subscriberIds as $subscriberId) {
        $recipients->push([
            'campaign_id'   => $campaign->id,
            'user_id'       => null,
            'subscriber_id' => $subscriberId,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    if ($recipients->isNotEmpty()) {
        CampaignRecipient::insert($recipients->toArray());
    }

    return response()->json([
        'message'      => 'Campaign created successfully',
        'campaign_id'  => $campaign->id,
        'user_count'   => $userIds->count(),
        'subscriber_count' => $subscriberIds->count(),
    ]);
}



public function update(Request $request)
{
    $campaign = Campaign::findOrFail($request->id);

    // Update campaign fields
    $campaign->update([
        'seller_id'     => null,
        'name'          => $request->name,
        'subject'       => $request->subject,
        'preview_text'  => $request->preview_text,
        'content'       => $request->content,
        'send_time'     => $request->send_time,
        'status'        => $request->status,
    ]);

    // Remove old campaign segments and recipients
    CampaignSegment::where('campaign_id', $campaign->id)->delete();
    CampaignRecipient::where('campaign_id', $campaign->id)->delete();

    // Recalculate based on selected segments
    $allMatchedUsers = collect();
    $allMatchedSubscribers = collect();

    // Load once
    $allUsers = User::with('order')->get();
    $allSubscribers = Subscriber::all();

    foreach ($request->segment_ids as $segment_id) {
        CampaignSegment::create([
            'campaign_id' => $campaign->id,
            'segment_id'  => $segment_id,
        ]);

        $segment = Segment::findOrFail($segment_id);

        if ($segment->segment_type === 'subscriber') {
            $matched = $allSubscribers->filter(function ($subscriber) use ($segment) {
                return $this->evaluateRules($subscriber, $segment->rules);
            });

            $allMatchedSubscribers = $allMatchedSubscribers->merge($matched);

        } else { // user segment
            $matched = $allUsers->filter(function ($user) use ($segment) {
                return $this->evaluateRules($user, $segment->rules);
            });

            $allMatchedUsers = $allMatchedUsers->merge($matched);
        }
    }

    // Unique IDs
    $userIds = $allMatchedUsers
        ->pluck('id')
        ->filter()
        ->unique()
        ->values();

    $subscriberIds = $allMatchedSubscribers
        ->pluck('id')
        ->filter()
        ->unique()
        ->values();

    $recipients = collect();

    foreach ($userIds as $userId) {
        $recipients->push([
            'campaign_id'   => $campaign->id,
            'user_id'       => $userId,
            'subscriber_id' => null,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    foreach ($subscriberIds as $subscriberId) {
        $recipients->push([
            'campaign_id'   => $campaign->id,
            'user_id'       => null,
            'subscriber_id' => $subscriberId,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    if ($recipients->isNotEmpty()) {
        CampaignRecipient::insert($recipients->toArray());
    }

    return response()->json([
        'message'          => 'Campaign updated successfully',
        'campaign_id'      => $campaign->id,
        'user_count'       => $userIds->count(),
        'subscriber_count' => $subscriberIds->count(),
    ]);
}




    public function delete($id) {

        Campaign::find($id)->delete();

        return response()->json(['message'=>'deleted successfully',200]);
    }

    public function multi_delete(Request $request)
    {
        Campaign::whereIn('id',$request->ids)->delete();


        $response = ['status'=>true,"message" => "Campaigns Deleted Successfully!"];
        return response($response, 200);
    }

    public function sendCampaign($id)
    {
        $campaign = Campaign::findOrFail($id);

        // Send to Users
        foreach ($campaign->userRecipients as $user) {
            SendCampaignEmail::dispatch($campaign->id, $user->id, null);
        }

        // Send to Subscribers
        foreach ($campaign->subscriberRecipients as $subscriber) {
            SendCampaignEmail::dispatch($campaign->id, null, $subscriber->id);
        }

        return response()->json(['message' => 'Campaign emails queued successfully.']);
    }


        private function evaluateRules($user, $rulesGroup)
        {
            
            $matchType = $rulesGroup['match_type'] ?? 'AND';
            $rules = $rulesGroup['rules'] ?? [];

            $results = [];

            foreach ($rules as $rule) {
                $relation = $rule['relation'] ?? null;
                $field = $rule['field'] ?? null;
                $aggregate = $rule['aggregate'] ?? null;
                $operator = $rule['operator'] ?? '=';
                $value = $rule['value'] ?? null;

                if (is_null($field)) {
                    $results[] = false;
                    continue;
                }

                // Case 1: No relation, direct user field
                if (is_null($relation)) {
                    $result = data_get($user, $field);
                } else {
                if (!method_exists($user, $relation)) {
                    $results[] = false;
                    continue;
                }

                if (is_null($aggregate)) {
                    $related = $user->$relation;

                    if (!$related) {
                        $results[] = false;
                        continue;
                    }

                    $result = data_get($related, $field);
                    } else {
                        $relationQuery = $user->$relation();

                        switch ($aggregate) {
                            case 'sum': $result = $relationQuery->sum($field); break;
                            case 'count': $result = $relationQuery->count(); break;
                            case 'avg': $result = $relationQuery->avg($field); break;
                            case 'min': $result = $relationQuery->min($field); break;
                            case 'max': $result = $relationQuery->max($field); break;
                            case 'first':
                                $related = $relationQuery->orderBy('id')->first();
                                $result = $related ? data_get($related, $field) : null;
                                break;
                            case 'last':
                                $related = $relationQuery->orderByDesc('id')->first();
                                $result = $related ? data_get($related, $field) : null;
                                break;
                            case 'exists': $result = $relationQuery->exists(); break;
                            case 'distinct_count': $result = $relationQuery->distinct($field)->count($field); break;
                            default:
                                $results[] = false;
                                continue 2; // exit this switch+foreach cleanly
                        }
                    }
                }

                $results[] = $this->compare($result, $operator, $value);
            }

            return $matchType === 'AND'
                ? !in_array(false, $results, true)
                : in_array(true, $results, true);
        }


    private function compare($left, $operator, $right)
    {
        return match ($operator) {
            '>' => $left > $right,
            '>=' => $left >= $right,
            '<' => $left < $right,
            '<=' => $left <= $right,
            '=' => $left == $right,
            '!=' => $left != $right,
            default => false,
        };
    }

}
