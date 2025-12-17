<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrackingEvent;
use App\Models\LinkStat;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    /**
     * OPEN TRACKING PIXEL
     */
    public function open(Request $request)
    {
        $campaignId    = $request->campaign_id;
        $recipientType = $request->recipient_type; // user | subscriber
        $recipientId   = $request->recipient_id;

        TrackingEvent::create([
            'campaign_id'   => $campaignId,
            'user_id'       => $recipientType === 'user' ? $recipientId : null,
            'subscriber_id' => $recipientType === 'subscriber' ? $recipientId : null,
            'event_type'    => 'open',
            'created_at'    => now(),
        ]);

        return response()->file(public_path('images/pixel.png'));
    }


    /**
     * CLICK TRACKING
     */
    public function click(Request $request)
    {
        $campaignId    = $request->campaign_id;
        $recipientType = $request->recipient_type;
        $recipientId   = $request->recipient_id;
        $linkId        = $request->link_id;

        $link = LinkStat::find($linkId);
        if (!$link) abort(404);

        $link->increment('clicks');

        $column = $recipientType === 'user' ? 'user_id' : 'subscriber_id';

        $alreadyClicked = TrackingEvent::where([
            'campaign_id'   => $campaignId,
            $column         => $recipientId,
            'event_type'    => 'click',
            'url'           => $link->url
        ])->exists();

        if (!$alreadyClicked) {
            $link->increment('unique_clicks');
        }

        TrackingEvent::create([
            'campaign_id'   => $campaignId,
            'user_id'       => $recipientType === 'user' ? $recipientId : null,
            'subscriber_id' => $recipientType === 'subscriber' ? $recipientId : null,
            'event_type'    => 'click',
            'url'           => $link->url,
            'created_at'    => now(),
        ]);

        return redirect()->away($link->url);
    }


    /**
     * UNSUBSCRIBE
     */
    public function unsubscribe(Request $request)
    {
        $campaignId    = $request->campaign_id;
        $recipientType = $request->recipient_type;
        $recipientId   = $request->recipient_id;

        $column = $recipientType === 'user' ? 'user_id' : 'subscriber_id';

        DB::table('campaign_recipients')
            ->where('campaign_id', $campaignId)
            ->where($column, $recipientId)
            ->update(['unsubscribed' => true]);

        TrackingEvent::create([
            'campaign_id'   => $campaignId,
            'user_id'       => $recipientType === 'user' ? $recipientId : null,
            'subscriber_id' => $recipientType === 'subscriber' ? $recipientId : null,
            'event_type'    => 'unsubscribe',
            'created_at'    => now()
        ]);

        return response('<h3>You have been unsubscribed.</h3>');
    }
}
