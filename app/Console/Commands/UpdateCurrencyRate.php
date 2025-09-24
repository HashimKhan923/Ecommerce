<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\CurrencyRate;

class UpdateCurrencyRate extends Command
{
    protected $signature = 'currency:update';
    protected $description = 'Fetch USD to AED rate and update database';

    public function handle()
    {
        try {
            $response = Http::get('https://api.exchangerate.host/convert', [
                'from'   => 'USD',
                'to'     => 'AED',
                'access_key' => config('services.exchangerate.key'),
            ]);

            $data = $response->json();

            if (isset($data['result'])) {
                $rate = $data['result'];

                CurrencyRate::updateOrCreate(
                    [
                        'base_currency'   => 'USD',
                        'target_currency' => 'AED',
                    ],
                    [
                        'rate' => $rate,
                    ]
                );

                $this->info("Exchange rate updated: 1 USD = {$rate} AED");
            } else {
                $this->error("Failed to fetch rate: " . json_encode($data));
            }

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}

