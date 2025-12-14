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

public function searchSite($site, $keywords)
{
    $queries = [
        "inurl:product $keywords site:$site",
        "inurl:products $keywords site:$site",
        "$keywords LED tail lights site:$site",
        "$keywords tail lamp site:$site",
        "$keywords Camry tail lights site:$site",
        "Camry tail lights site:$site",
        "Toyota Camry tail lights site:$site",
    ];

    foreach ($queries as $q) {

        \Log::info("Trying SerpAPI query: $q");

        $response = Http::get($this->endpoint, [
            'engine' => 'google',
            'q' => $q,
            'num' => 10,
            'api_key' => $this->key
        ]);

        $results = $response->json()['organic_results'] ?? [];

        // Only return if real results exist
        if (count($results) > 0) {
            return $results;
        }
    }

    return [];
}


}
