<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AiTrendingProduct;
use Illuminate\Support\Facades\Http;

class AiTrendingProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trending:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
         AiTrendingProduct::truncate();

$prompt = <<<EOT
Give me a list of the top 10 trending auto parts worldwide right now.

Rules:
- Each keyword must include a car make and model (e.g., "Honda Accord", "Toyota Camry", "Ford F-150", "BMW X5").
- Then add the part name (e.g., "headlights", "brake pads", "tail lights").
- Each keyword phrase should be 3 to 4 words total.
- Do not give generic items without a make and model.
- No descriptions, numbers, or extra text — just the keyword phrase.
- Format as a simple list, one per line.
EOT;

        $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are an expert in the auto parts industry.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $trendingParts = $response['choices'][0]['message']['content'];

        // Save each item to DB
        foreach (explode("\n", $trendingParts) as $part) {
            $name = preg_replace('/^\d+\.?\s*/', '', trim($part)); // remove "1." or "1. "
            if ($name) {
                AiTrendingProduct::create(['names' => $name]);
            }
        }

        // Optional log
        info('Trending auto parts updated successfully!');

    }
}
