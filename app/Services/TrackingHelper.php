<?php

namespace App\Services;

use App\Models\LinkStat;
use Illuminate\Support\Facades\Http;


class TrackingHelper
{
    function injectTracking($html, $campaignId, $userId)
    {
        $baseUrl = 'https://api.dragonautomart.com';

        // Open tracking pixel
        $pixel = "<img src=\"{$baseUrl}/api/track/open/{$campaignId}/{$userId}\" width=\"1\" height=\"1\" style=\"display:none;\" />";
        $html .= $pixel;

        $html = preg_replace_callback('/<a\s+href="([^"]+)"/i', function ($matches) use ($campaignId, $userId, $baseUrl) {
            $originalUrl = $matches[1];

            // ✅ Add campaign_id as query param
            $parsed = parse_url($originalUrl);

            // Check if URL already has query params
            $query = isset($parsed['query']) ? $parsed['query'] . '&' : '';
            $query .= 'campaign_id=' . $campaignId;

            // Rebuild URL with new query
            $newUrl = (isset($parsed['scheme']) ? "{$parsed['scheme']}://" : '') .
                    (isset($parsed['host']) ? "{$parsed['host']}" : '') .
                    (isset($parsed['path']) ? "{$parsed['path']}" : '') .
                    '?' . $query;

            if (isset($parsed['fragment'])) {
                $newUrl .= '#' . $parsed['fragment'];
            }

            // ✅ Save link in DB with appended campaign_id
            $link = LinkStat::firstOrCreate([
                'campaign_id' => $campaignId,
                'url' => $newUrl
            ]);

            // ✅ Replace with your tracked redirect link
            $trackUrl = "{$baseUrl}/api/track/click/{$campaignId}/{$userId}/{$link->id}";

            return '<a href="' . $trackUrl . '"';
        }, $html);

        return $html;
    }




}
