<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Http;

class AIKeywordExtractor
{
public function extract($title)
{
    $prompt = "
        Extract model, make, year, chassis code, and part type keywords.
        Return ONLY comma-separated keywords. No sentences.

        Title: \"$title\"
    ";

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        'Content-Type'  => 'application/json',
    ])->post('https://api.openai.com/v1/chat/completions', [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'system', 'content' => 'You are an expert in the auto parts industry.'],
            ['role' => 'user', 'content' => $prompt],
        ],
    ]);

    $keywords = $response->json('choices.0.message.content');

    return array_map('trim', explode(',', $keywords));
}
}
