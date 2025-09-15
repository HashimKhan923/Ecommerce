<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Seller;
use App\Models\Order;
use App\Models\OrderForecast;
use OpenAI\Laravel\Facades\OpenAI;
use Carbon\Carbon;

class GenerateOrderForecasts extends Command
{
    protected $signature = 'forecast:generate';
    protected $description = 'Generate 6-month AI forecasts for all sellers';

    public function handle()
    {
        $sellers = User::where('user_type', 'seller')->get();

        foreach ($sellers as $seller) {
            $this->info("Processing seller: {$seller->id} - {$seller->name}");

            // Get past 12 months data
            $orders = Order::where('seller_id', $seller->id)
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total_orders, SUM(amount) as total_revenue')
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();

            if ($orders->count() < 3) {
                $this->warn("Skipping seller {$seller->id}, not enough data");
                continue;
            }

            // Prepare data for AI
            $data = $orders->map(fn($o) => [
                'month' => $o->month,
                'orders' => $o->total_orders,
                'revenue' => $o->total_revenue,
            ])->toArray();

            $prompt = "
            You are a sales forecasting assistant.
            Based on this past 12 months data:\n" . json_encode($data) . "\n
            Predict the next 6 months:
            - month (YYYY-MM)
            - predicted_orders
            - predicted_revenue
            - a one-sentence business insight
            Return only JSON array.
            ";

            try {
                $response = OpenAI::chat()->create([
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are an AI that generates numeric forecasts.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.3
                ]);

                $content = $response['choices'][0]['message']['content'] ?? '[]';
                $predictions = json_decode($content, true);

                if (!is_array($predictions)) {
                    $this->error("Invalid AI response for seller {$seller->id}");
                    continue;
                }

                foreach ($predictions as $row) {
                    OrderForecast::updateOrCreate(
                        [
                            'seller_id' => $seller->id,
                            'month'     => $row['month'],
                        ],
                        [
                            'predicted_orders'  => $row['predicted_orders'],
                            'predicted_revenue' => $row['predicted_revenue'],
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
