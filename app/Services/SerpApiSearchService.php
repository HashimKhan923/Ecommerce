<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SerpApiSearchService
{
    protected $key;
    protected $endpoint;

    public function __construct()
    {
        $this->key = config('services.serpapi.key');
        $this->endpoint = config('services.serpapi.endpoint');
    }

public function searchSite($site, $query)
{
    $queries = [
        "inurl:products $query site:$site",
        "inurl:product $query site:$site",
        "$query site:$site",
        "$query $site",
        "$query $site LED tail lights",
    ];

    foreach ($queries as $q) {
        $response = Http::get($this->endpoint, [
            'engine' => 'google',
            'q' => $q,
            'num' => 10,
            'api_key' => $this->key
        ]);

        $results = $response->json()['organic_results'] ?? [];

        \Log::info("Query tried: $q");
        \Log::info("Results count: " . count($results));

        if (!empty($results)) {
            return $results;
        }
    }

    return [];
}

}
