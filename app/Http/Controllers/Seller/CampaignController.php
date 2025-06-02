<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Jobs\SendCampaignEmail;
class CampaignController extends Controller
{
    public function index($seller_id) {

        $campaigns = Campaign::where('seller_id', $seller_id)->get();

        return response()->json(['campaigns'=>$campaigns]);
    }

    public function detail($id) {

        $campaign = Campaign::find($id);

        $links = LinkStat::where('campaign_id', $id)->get();


        return response()->json(['campaign'=>$campaign,'links'=>$links]);
    }

    public function create(Request $request)
    {
        // Create the campaign
        $campaign = Campaign::create([
            'seller_id'     => $request->seller_id,
            'name'          => $request->name,
            'subject'       => $request->subject,
            'preview_text'  => $request->preview_text,
            'content'       => $request->content,
            'send_time'     => $request->send_time,
            'status'        => 'scheduled' // Optional but useful
        ]);

        // Prepare recipients
        $recipients = collect($request->recipient_ids)->map(function ($userId) use ($campaign) {
            return [
                'campaign_id' => $campaign->id,
                'user_id'     => $userId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        });

        // Insert all recipients in one query
        CampaignRecipient::insert($recipients->toArray());

        return response()->json([
            'message' => 'Campaign created successfully',
        ]);
    }


    public function update(Request $request, $id)
    {
        // Find campaign
        $campaign = Campaign::findOrFail($id);

        // Update campaign fields
        $campaign->update([
            'seller_id'     => $request->seller_id,
            'name'          => $request->name,
            'subject'       => $request->subject,
            'preview_text'  => $request->preview_text,
            'content'       => $request->content,
            'send_time'     => $request->send_time,
            'status'        => 'scheduled' // Optional
        ]);

        // Remove old recipients
        CampaignRecipient::where('campaign_id', $campaign->id)->delete();

        // Add updated recipients
        $recipients = collect($request->recipient_ids)->map(function ($userId) use ($campaign) {
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
        ]);
    }


    public function delete($id) {

        Campaign::find($id)->delete();

        return response()->json(['message'=>'deleted successfully',200]);
    }

    public function sendCampaign(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        foreach ($campaign->recipients as $user) {
            SendCampaignEmail::dispatch($campaign->id, $user->id);
        }

        return response()->json(['message' => 'Campaign emails queued successfully.']);
    }

}
