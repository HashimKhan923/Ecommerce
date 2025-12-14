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
        // Extract keywords from product title
        $keywords = implode(' ', (new AIKeywordExtractor())->extract($productTitle));

        $stores = [
'xgenauto.com',
'swaautosports.com'
      ];

        $search = new SerpApiSearchService();
        $scraper = new ProductScraper();
        $matcher = new ProductMatcher();

        $results = [];

        foreach ($stores as $store) {

            $searchResults = $search->searchSite($store, $keywords);

foreach ($searchResults as $item) {

    $url = $item['link'] ?? null;
    if (!$url) continue;

    // FILTER NON-PRODUCT PAGES
    if (
        str_contains($url, '/category') ||
        str_contains($url, '/tag') ||
        str_contains($url, '/search') ||
        (str_contains($url, '/collections/') && !str_contains($url, '/products/'))
    ) {
        continue;
    }

    // SCRAPE PRODUCT DETAILS
    $details = $scraper->scrape($url);

    // SCORE MATCH
    $score = $matcher->score($productTitle, $details['title']);
    if ($score >= 0.40) {  // use 0.40 for testing, increase later
        $results[] = [
            'store' => $store,
            'match_score' => $score,
            'title'       => $details['title'],
            'price'       => $details['price'],
            'image'       => $details['image'],
            'url'         => $details['url']
        ];
    }
}

        }

        return response()->json([
            'status' => 'success',
            'product' => $productTitle,
            'comparisons' => $results
        ]);
    }
}
