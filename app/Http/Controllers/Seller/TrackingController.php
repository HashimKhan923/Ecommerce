<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrackingEvent;
use App\Models\LinkStat;
use DB;

class TrackingController extends Controller
{
        // Handle open tracking pixel
    public function open($campaignId, $userId) {
        TrackingEvent::create([
            'campaign_id' => $campaignId,
            'user_id' => $userId,
            'event_type' => 'open',
        ]);
        // Return a 1x1 transparent pixel image
        return response()->file(public_path('images/pixel.png'));
    }

    // Handle link click
    public function click($campaignId, $userId, $linkId) {
        $link = LinkStat::find($linkId);
        if ($link) {
            // Update click stats
            $link->increment('clicks');
            // Unique click?
            $already = TrackingEvent::where(['campaign_id'=>$campaignId,'user_id'=>$userId,'event_type'=>'click','url'=>$link->url])->exists();
            if (!$already) {
                $link->increment('unique_clicks');
            }
            // Log event
            TrackingEvent::create([
                'campaign_id' => $campaignId,
                'user_id' => $userId,
                'event_type' => 'click',
                'url' => $link->url,
            ]);
            // Redirect to actual URL
            return redirect()->away($link->url);
        }
        abort(404);
    }

    // Handle unsubscribe link
    public function unsubscribe($campaignId, $userId) {
        // Mark pivot unsubscribed
        DB::table('campaign_recipients')
          ->where('campaign_id', $campaignId)
          ->where('user_id', $userId)
          ->update(['unsubscribed' => true]);
        TrackingEvent::create([
            'campaign_id' => $campaignId,
            'user_id' => $userId,
            'event_type' => 'unsubscribe'
        ]);
        return response('You have been unsubscribed.');
    }
}
