<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;

class ProductMatcher
{
    public function score($original, $candidate)
    {
        $prompt = "
        Compare the following auto parts for compatibility.
        Output only a number 0.0 to 1.0.

        Original: $original
        Candidate: $candidate
        ";

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ]
        ]);

        return floatval($response['choices'][0]['message']['content']);
    }
}
