<?php

namespace App\Services;

use App\Models\LinkStat;
use Illuminate\Support\Facades\Http;


class TrackingHelper
{
    function injectTracking($html, $campaignId, $userId) {
        // Append open-pixel
        $pixel = "<img src=\"" . route('track.open', ['campaign'=>$campaignId,'user'=>$userId]) . "\" width=\"1\" height=\"1\" />";
        $html .= $pixel;
        // Replace <a> links with tracking route
        return preg_replace_callback('/<a href="([^"]+)"/', function($matches) use ($campaignId,$userId) {
            $url = $matches[1];
            // Create or update link_stats
            $link = LinkStat::firstOrCreate(['campaign_id'=>$campaignId,'url'=>$url]);
            // Build tracking URL (passing link id and user id)
            $trackUrl = route('track.click', ['campaign'=>$campaignId,'user'=>$userId,'link'=>$link->id]);
            return '<a href="'.$trackUrl.'"';
        }, $html);
    }



}
