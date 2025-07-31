<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class AiProductController extends Controller
{
        public function generateProductDescription(Request $request)
    {


        $productName = $request->input('product_name');
        $features = $request->input('features', []);
        $tone = $request->input('tone', 'professional');


        $prompt = "Write a compelling product description in a {$tone} tone for a product named '{$productName}'";
        if (!empty($features)) {
            $prompt .= " with the following features: " . implode(', ', $features);
        }

        try {
            $response = Http::withToken(env('OPENAI_API_KEY'))
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo', // You can switch to 'gpt-4' if available
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are an expert in writing car part product descriptions.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 180,
                ]);

            $data = $response->json();

            if ($response->successful() && isset($data['choices'][0]['message']['content'])) {
                return response()->json([
                    'success' => true,
                    'product_name' => $productName,
                    'features' => $features,
                    'description' => $data['choices'][0]['message']['content'],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'OpenAI API error',
                    'error' => $data['error']['message'] ?? 'Unknown error',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Exception occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


     public function generateSeoMeta($productName)
    {


        

        $prompt = <<<EOT
    You are an SEO expert. Based on the product name "$productName", generate the following:
    1. meta_title (max 60 characters),
    2. meta_description (between 150–160 characters),
    3. meta_keywords: a JSON array of 10 keyword phrases (each 2–5 words).

    Respond ONLY in the following JSON format:

    {
    "meta_title": "string",
    "meta_description": "string",
    "meta_keywords": ["keyword 1", "keyword 2", "..."]
    }
    EOT;

        try {


            
        $response = Http::withToken(env('OPENAI_API_KEY'))->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You generate SEO metadata.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $content = $response['choices'][0]['message']['content'] ?? '{}';

        $data = json_decode($content, true);

        if (
            !isset($data['meta_title']) ||
            !isset($data['meta_description']) ||
            !isset($data['meta_keywords']) ||
            !is_array($data['meta_keywords'])
        ) {
            throw new \Exception('Invalid response format from AI.');
        }

        return response()->json([
            'success' => true,
            'product_name' => $productName,
            'meta_title' => $data['meta_title'],
            'meta_description' => $data['meta_description'],
            'meta_keywords' => $data['meta_keywords'],
        ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate SEO keywords.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
