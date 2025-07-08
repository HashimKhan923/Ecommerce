<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CohereService
{
    public function generateProductDescription($productName, $features = [])
    {
        $prompt = "Write a detailed and persuasive product description for an auto part named '{$productName}'. ";
        if (!empty($features)) {
            $prompt .= "This part has these features: " . implode(', ', $features) . ". ";
        }
        $prompt .= "Mention vehicle compatibility, benefits, and why it’s important for performance.";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('COHERE_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.cohere.ai/v1/generate', [
            'model' => 'command-r', // Use free "command-r" or "command-r-plus" if you have access
            'prompt' => $prompt,
            'max_tokens' => 300,
            'temperature' => 0.7,
        ]);

        if (!$response->successful()) {
            \Log::error('Cohere API Error', ['response' => $response->json()]);
            return 'Description not available (Cohere API error).';
        }

        return trim($response->json()['generations'][0]['text'] ?? 'Description not available.');
    }
}
