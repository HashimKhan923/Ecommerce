<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AIKeywordExtractor;
use App\Services\SerpApiSearchService;
use App\Services\ProductScraper;
use App\Services\ProductMatcher;

class ProductComparisonController extends Controller
{
    public function compare($productTitle)
{
    $stores = [
        'vlandshop.com',
        'carid.com',
        'alphardsupply.com',
        'carparts.com'
    ];

    $keywords = implode(' ', (new AIKeywordExtractor())->extract($productTitle));

    $search = new SerpApiSearchService();
    $scraper = new ProductScraper();
    $matcher = new ProductMatcher();

    $results = [];
    $searchErrors = [];

    foreach ($stores as $store) {

        // 1. SEARCH USING SERPAPI
        $searchResults = $search->searchSite($store, $keywords);

        if (empty($searchResults)) {
            $searchErrors[] = "No search results found for $store";
            continue;
        }

        foreach ($searchResults as $item) {

            $url = $item['link'] ?? null;

            if (!$url) {
                $searchErrors[] = "Invalid result structure from $store";
                continue;
            }

            // FILTER BAD URLS
            if (
                str_contains($url, '/category') ||
                str_contains($url, '/tag') ||
                str_contains($url, '/search') ||
                (str_contains($url, '/collections/') && !str_contains($url, '/products/'))
            ) {
                continue;
            }

            // 2. SCRAPE PRODUCT PAGE
            $details = $scraper->scrape($url);

            if (empty($details['title'])) {
                $searchErrors[] = "Scraper failed on URL: $url";
                continue;
            }

            // 3. MATCH SCORE
            $score = $matcher->score($productTitle, $details['title']);

            if ($score >= 0.40) {
                $results[] = [
                    'store'       => $store,
                    'match_score' => $score,
                    'title'       => $details['title'],
                    'price'       => $details['price'],
                    'image'       => $details['image'],
                    'url'         => $details['url']
                ];
            }
        }
    }

    // RETURN RESULTS OR ERROR
    if (empty($results)) {
        return response()->json([
            'status'  => 'error',
            'message' => 'No matching products found.',
            'errors'  => $searchErrors
        ], 404);
    }

    return response()->json([
        'status'      => 'success',
        'product'     => $productTitle,
        'comparisons' => $results
    ]);
}

}
