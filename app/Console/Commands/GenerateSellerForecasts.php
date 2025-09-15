<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\OrderForcast;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class GenerateSellerForecasts extends Command
{
    protected $signature = 'forecast:generate';
    protected $description = 'Generate 6-month AI forecasts for all sellers';

    public function handle()
    {
        $sellers = User::where('user_type', 'seller')->get();

        foreach ($sellers as $seller) {
            $this->info("Processing seller: {$seller->id} - {$seller->name}");

            // Get past 12 months data
            $orders = Order::where('sellers_id', $seller->id)
                ->where('created_at', '>=', now()->subMonths(12))
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total_orders, SUM(amount) as total_revenue')
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();

            if ($orders->count() < 3) {
                $this->warn("Skipping seller {$seller->id}, not enough data");
                continue;
            }

            // Prepare historical data
            $data = $orders->map(fn($o) => [
                'month'   => $o->month,
                'orders'  => $o->total_orders,
                'revenue' => $o->total_revenue,
            ])->toArray();

            // Generate fixed next 6 months
            $months = [];
            for ($i = 1; $i <= 6; $i++) {
                $months[] = now()->addMonths($i)->format('Y-m');
            }

            $prompt = "
            You are an AI that predicts sales.
            Input: past 12 months orders and revenue.
            Task: Predict exactly the next 6 months starting from the next calendar month.
            Months: " . json_encode($months) . "
            
            Output: ONLY a valid JSON array with exactly this structure:
            [
              {\"month\": \"YYYY-MM\", \"predicted_orders\": 123, \"predicted_revenue\": 4567.89, \"insight\": \"short insight\"}
            ]

            No text before or after JSON. 
            Data: " . json_encode($data);

            try {
                $response = Http::withToken(env('OPENAI_API_KEY'))
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-4o-mini',
                        'messages' => [
                            ['role' => 'system', 'content' => 'You are an AI that generates numeric forecasts.'],
                            ['role' => 'user', 'content' => $prompt],
                        ],
                        'temperature' => 0.3,
                    ]);

                $content = $response['choices'][0]['message']['content'] ?? '[]';
                $predictions = json_decode($content, true);

                if (!is_array($predictions)) {
                    $this->error("Invalid AI response for seller {$seller->id}");
                    continue;
                }

                // Save predictions, enforce correct months
                foreach ($months as $index => $month) {
                    $row = $predictions[$index] ?? null;
                    if (!$row) continue;

                    OrderForcast::updateOrCreate(
                        [
                            'seller_id' => $seller->id,
                            'month'     => $month,
                        ],
                        [
                            'predicted_orders'  => $row['predicted_orders'] ?? 0,
                            'predicted_revenue' => $row['predicted_revenue'] ?? 0,
                            'insight'           => $row['insight'] ?? null,
                        ]
                    );
                }

                $this->info("✅ Forecast saved for seller {$seller->id}");

            } catch (\Exception $e) {
                $this->error("❌ Error for seller {$seller->id}: " . $e->getMessage());
            }
        }
    }
}
