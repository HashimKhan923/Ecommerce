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
            'swaautosports.com',
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

                // Scrape real product details
                $details = $scraper->scrape($url);

                // Score match using productTitle
                $score = $matcher->score($productTitle, $details['title']);

                if ($score >= 0.70) {
                    $results[] = [
                        'store' => $store,
                        'match_score' => $score,
                        'title' => $details['title'],
                        'price' => $details['price'],
                        'image' => $details['image'],
                        'url' => $details['url']
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
