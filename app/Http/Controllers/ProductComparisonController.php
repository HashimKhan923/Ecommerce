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
        $prompt = "
            You are an AI product comparison engine.

            Task:
            - Search these websites for the product: $productTitle
            - Websites:
                - xgenauto.com
                - vlandshop.com
                - carid.com
                - alphardsupply.com
            - Return ONLY JSON with:
                - store
                - title
                - price
                - image
                - url
                - match_score (0–1)

            If not found on a store, omit it.
        ";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type'  => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4.1',    // web browsing enabled
            'messages' => [
                ['role' => 'system', 'content' => 'You are an automotive product scraper and search agent.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $data = $response->json('choices.0.message.content');

        return response()->json([
            'status' => 'success',
            'product' => $productTitle,
            'comparisons' => json_decode($data, true)
        ]);
    }
}
