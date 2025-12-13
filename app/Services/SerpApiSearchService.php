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
        $params = [
            'engine' => 'google',
            'q' => "site:$site $query",
            'api_key' => $this->key,
            'num' => 10
        ];

        $response = Http::get($this->endpoint, $params);
        return $response->json()['organic_results'] ?? [];
    }
}
