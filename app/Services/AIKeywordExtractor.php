<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;

class AIKeywordExtractor
{
    public function extract($title)
    {
        $prompt = "
            Extract search terms for model/make/year + part.
            Return comma-separated keywords only.

            Title: \"$title\"
        ";

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ]
        ]);

        return explode(',', $response['choices'][0]['message']['content']);
    }
}
