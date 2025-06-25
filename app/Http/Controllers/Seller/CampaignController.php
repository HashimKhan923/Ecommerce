<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Models\CampaignSegment;
use App\Models\LinkStat;
use App\Models\Segment;
use App\Models\MyCustomer;
use App\Jobs\SendCampaignEmail;
use Carbon\Carbon;
class CampaignController extends Controller
{
    public function index($seller_id) {


        $now = Carbon::now();
        $campaigns = Campaign::with('segments','trackingEvents','opens','clicks')->where('seller_id', $seller_id)->get();

        return response()->json(['campaigns'=>$campaigns,'time'=>$now]);
    }

    public function detail($id) {

        $campaign = Campaign::with('segments','trackingEvents','opens','clicks')->where('id',$id)->first();;

        $links = LinkStat::where('campaign_id', $id)->get();


        return response()->json(['campaign'=>$campaign,'links'=>$links]);
    }

    public function create(Request $request)
    {
        $campaign = Campaign::create([
            'seller_id'     => $request->seller_id,
            'name'          => $request->name,
            'subject'       => $request->subject,
            'preview_text'  => $request->preview_text,
            'content'       => $request->content,
            'send_time'     => $request->send_time,
            'status'        => $request->status,
        ]);

        $allMatchedCustomers = collect();

        foreach ($request->segment_ids as $segment_id) {
            $campaignSegment = CampaignSegment::create([
                'campaign_id' => $campaign->id,
                'segment_id' => $segment_id,
            ]);

            $segment = Segment::findOrFail($segment_id);

            $customers = MyCustomer::with('customer', 'orders')
                ->where('seller_id', $segment->seller_id)
                ->get();

            $matched = $customers->filter(function ($customer) use ($segment) {
                return $this->evaluateRules($customer, $segment->rules);
            });

            $allMatchedCustomers = $allMatchedCustomers->merge($matched);
        }

       $recipient_ids = $allMatchedCustomers->pluck('customer_id')->filter()->unique()->toArray();


            $recipients = collect($recipient_ids)->map(function ($userId) use ($campaign) {
                return [
                        'campaign_id' => $campaign->id,
                        'user_id'     => $userId,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                });





        CampaignRecipient::insert($recipients->toArray());

        return response()->json([
            'message' => 'Campaign created successfully',
        ]);
    }


    public function update(Request $request)
    {
        $campaign = Campaign::findOrFail($request->id);

        // Update campaign fields
        $campaign->update([
            'seller_id'     => $request->seller_id,
            'name'          => $request->name,
            'subject'       => $request->subject,
            'preview_text'  => $request->preview_text,
            'content'       => $request->content,
            'send_time'     => $request->send_time,
            'status'        => $request->status,
        ]);

        // 🔁 Remove old campaign segments and recipients
        CampaignSegment::where('campaign_id', $campaign->id)->delete();
        CampaignRecipient::where('campaign_id', $campaign->id)->delete();

        // 🔍 Recalculate based on selected segments
        $allMatchedCustomers = collect();

        foreach ($request->segment_ids as $segment_id) {
            CampaignSegment::create([
                'campaign_id' => $campaign->id,
                'segment_id' => $segment_id,
            ]);

            $segment = Segment::findOrFail($segment_id);

            $customers = MyCustomer::with('customer', 'orders')
                ->where('seller_id', $segment->seller_id)
                ->get();

            $matched = $customers->filter(function ($customer) use ($segment) {
                return $this->evaluateRules($customer, $segment->rules);
            });

            $allMatchedCustomers = $allMatchedCustomers->merge($matched);
        }

        $recipient_ids = $allMatchedCustomers
            ->pluck('customer_id')
            ->filter()
            ->unique()
            ->toArray();

        $recipients = collect($recipient_ids)->map(function ($userId) use ($campaign) {
            return [
                'campaign_id' => $campaign->id,
                'user_id'     => $userId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        });

        CampaignRecipient::insert($recipients->toArray());

        return response()->json([
            'message' => 'Campaign updated successfully',
            'campaign_id' => $campaign->id
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

        foreach ($campaign->recipients as $user) {
            SendCampaignEmail::dispatch($campaign->id, $user->id);
        }

        return response()->json(['message' => 'Campaign emails queued successfully.']);
    }


        private function evaluateRules($customer, $rulesGroup)
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

                // Case 1: No relation, direct customer field
                if (is_null($relation)) {
                    $result = data_get($customer, $field);
                } else {
                if (!method_exists($customer, $relation)) {
                    $results[] = false;
                    continue;
                }

                if (is_null($aggregate)) {
                    $related = $customer->$relation;

                    if (!$related) {
                        $results[] = false;
                        continue;
                    }

                    $result = data_get($related, $field);
                    } else {
                        $relationQuery = $customer->$relation();

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
