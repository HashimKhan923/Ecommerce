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


        
        $campaigns = Campaign::with('segments','trackingEvents','opens','clicks')->withCount('allRecipients')->where('seller_id', null)->get();

        return response()->json(['campaigns'=>$campaigns]);
    }

    public function detail($id) {

        $campaign = Campaign::with('segments.segment','trackingEvents','opens','clicks')->withCount('allRecipients')->where('id',$id)->first();;

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


private function evaluateRules($customer, $rulesGroup)
{
    $matchType = $rulesGroup['match_type'] ?? 'AND';
    $rules = $rulesGroup['rules'] ?? [];

    // If no rules, match all customers
    if (empty($rules)) {
        return true;
    }

    $results = [];

    foreach ($rules as $rule) {
        $field = $rule['field'];
        $operator = $rule['operator'];
        $value = $rule['value'];

        $parts = explode('.', $field);

        if (count($parts) === 2) {
            [$relation, $fieldPart] = $parts;

            $aggregates = ['count', 'sum', 'avg', 'min', 'max', 'first', 'last', 'distinct_count'];

            if (in_array($fieldPart, $aggregates)) {
                $query = $customer->$relation();

                switch ($fieldPart) {
                    case 'count':
                        $actual = $query->count();
                        break;
                    case 'sum':
                        $sumField = $rule['sum_field'] ?? 'amount';
                        $actual = $query->sum($sumField);
                        break;
                    case 'avg':
                        $avgField = $rule['avg_field'] ?? 'amount';
                        $actual = $query->avg($avgField);
                        break;
                    case 'min':
                        $minField = $rule['min_field'] ?? 'amount';
                        $actual = $query->min($minField);
                        break;
                    case 'max':
                        $maxField = $rule['max_field'] ?? 'amount';
                        $actual = $query->max($maxField);
                        break;
                    case 'first':
                        $firstField = $rule['first_field'] ?? null;
                        $record = $query->orderBy('id')->first();
                        $actual = $record && $firstField ? data_get($record, $firstField) : null;
                        break;
                    case 'last':
                        $lastField = $rule['last_field'] ?? null;
                        $record = $query->orderByDesc('id')->first();
                        $actual = $record && $lastField ? data_get($record, $lastField) : null;
                        break;
                    case 'distinct_count':
                        $distinctField = $rule['distinct_field'] ?? null;
                        $actual = $distinctField ? $query->distinct($distinctField)->count($distinctField) : null;
                        break;
                    default:
                        $actual = null;
                }

                $results[] = $this->compare($actual, $operator, $value);
            } else {
                $related = $customer->$relation;

                if ($related instanceof \Illuminate\Support\Collection) {
                    // MANY: match ANY related record
                    $matchAny = $related->contains(function ($item) use ($fieldPart, $operator, $value) {
                        return $this->compare(data_get($item, $fieldPart), $operator, $value);
                    });
                    $results[] = $matchAny;
                } else {
                    // Single related row
                    $actual = data_get($related, $fieldPart);
                    $results[] = $this->compare($actual, $operator, $value);
                }
            }
        } else {
            // Direct customer field
            $actual = data_get($customer, $field);
            $results[] = $this->compare($actual, $operator, $value);
        }
    }

    return $matchType === 'AND'
        ? !in_array(false, $results, true)
        : in_array(true, $results, true);
}

    /**
     * Compare values with various operators.
     */
    private function compare($actual, $operator, $expected)
    {
        // normalize for string-based operators
        if (in_array($operator, ['contains', 'starts_with', 'ends_with'])) {
            $actual   = (string) $actual;
            $expected = (string) $expected;
        }

        switch ($operator) {
            case '=':
                return $actual == $expected;
            case '!=':
                return $actual != $expected;
            case '>':
                return $actual > $expected;
            case '<':
                return $actual < $expected;
            case '>=':
                return $actual >= $expected;
            case '<=':
                return $actual <= $expected;
            case 'contains':
                return stripos($actual, $expected) !== false;
            case 'starts_with':
                return stripos($actual, $expected) === 0;
            case 'ends_with':
                return str_ends_with(strtolower($actual), strtolower($expected));
            default:
                return false;
        }
    }

}
