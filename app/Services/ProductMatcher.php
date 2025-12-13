<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Http;

class ProductMatcher
{
public function score($original, $candidate)
{
    $prompt = "
        Compare these two auto parts for compatibility (model/make/year).
        Return ONLY a float number between 0.0 and 1.0.
        
        Original: $original
        Candidate: $candidate
    ";

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        'Content-Type'  => 'application/json',
    ])->post('https://api.openai.com/v1/chat/completions', [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'system', 'content' => 'You are an expert in auto parts compatibility.'],
            ['role' => 'user', 'content' => $prompt],
        ],
    ]);

    return floatval($response->json('choices.0.message.content'));
}

}
