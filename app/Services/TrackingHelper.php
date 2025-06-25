<?php

namespace App\Services;

use App\Models\LinkStat;
use Illuminate\Support\Facades\Http;


class TrackingHelper
{
function injectTracking($html, $campaignId, $userId)
{
    $baseUrl = 'https://api.dragonautomart.com'; // your static domain

    // Add open tracking pixel
    $pixel = "<img src=\"{$baseUrl}/api/track/open/{$campaignId}/{$userId}\" width=\"1\" height=\"1\" />";
    $html .= $pixel;

    // Replace all <a href="..."> with tracked versions
    return preg_replace_callback('/<a\s+href="([^"]+)"/i', function ($matches) use ($campaignId, $userId, $baseUrl) {
        $originalUrl = $matches[1];

        // Store or retrieve link in DB
        $link = LinkStat::firstOrCreate([
            'campaign_id' => $campaignId,
            'url' => $originalUrl
        ]);

        $trackUrl = "{$baseUrl}/api/track/click/{$campaignId}/{$userId}/{$link->id}";

        return '<a href="' . $trackUrl . '"';
    }, $html);
}




}
