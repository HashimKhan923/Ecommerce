<?php

namespace App\Services;

use App\Models\LinkStat;

class TrackingHelper
{
    public function injectTracking($html, $campaignId, $recipient)
    {
        $baseUrl = 'https://api.dragonautomart.com/api/track';

        $recipientType = $recipient instanceof \App\Models\User ? 'user' : 'subscriber';
        $recipientId   = $recipient->id;

        //////////////////////////////////////
        // 1. OPEN TRACKING PIXEL
        //////////////////////////////////////
        $pixelUrl = "{$baseUrl}/open?campaign_id={$campaignId}&recipient_type={$recipientType}&recipient_id={$recipientId}";
        $pixel = '<img src="'.$pixelUrl.'" width="1" height="1" style="display:none;" />';
        $html .= $pixel;

        //////////////////////////////////////
        // 2. CLICK TRACKING
        //////////////////////////////////////
        $html = preg_replace_callback('/<a\s+href="([^"]+)"/i', function ($matches) use ($campaignId, $recipientType, $recipientId, $baseUrl) {

            $realUrl = $matches[1];

            // Save or fetch LinkStat
            $link = LinkStat::firstOrCreate(
                ['campaign_id' => $campaignId, 'url' => $realUrl],
                ['clicks' => 0, 'unique_clicks' => 0]
            );

            $trackUrl =
                "{$baseUrl}/click?".
                "campaign_id={$campaignId}".
                "&link_id={$link->id}".
                "&recipient_type={$recipientType}".
                "&recipient_id={$recipientId}";

            return '<a href="'.$trackUrl.'"';
        }, $html);

        //////////////////////////////////////
        // 3. UNSUBSCRIBE LINK
        //////////////////////////////////////
        $unsubscribeUrl =
            "{$baseUrl}/unsubscribe?".
            "campaign_id={$campaignId}".
            "&recipient_type={$recipientType}".
            "&recipient_id={$recipientId}";

        $html = str_replace('{{unsubscribe_url}}', $unsubscribeUrl, $html);

        return $html;
    }
}
