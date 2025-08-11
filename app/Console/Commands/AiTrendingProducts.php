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

$prompt = "Give me a list of the top 10 trending auto parts worldwide right now.
Only include popular car makes and models like Toyota, Honda, Ford, BMW, etc.
Each item should be a short keyword phrase (2 to 3 words), without any descriptions or extra text.
Format the response as a simple list, one per line.";

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
