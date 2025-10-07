<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\CurrencyRate;

class UpdateCurrencyRate extends Command
{
    protected $signature = 'currency:update';
    protected $description = 'Fetch USD to AED and USD to CAD rates, and update database';

    public function handle()
    {
        // List of currencies you want to fetch
        $targetCurrencies = ['AED', 'CAD'];

        foreach ($targetCurrencies as $currency) {
            try {
                $response = Http::get('https://api.exchangerate.host/convert', [
                    'from'        => 'USD',
                    'to'          => $currency,
                    'amount'      => 1,
                    'access_key'  => config('services.exchangerate.key'),
                ]);

                $data = $response->json();

                if (isset($data['result'])) {
                    $rate = $data['result'];

                    CurrencyRate::updateOrCreate(
                        [
                            'base_currency'   => 'USD',
                            'target_currency' => $currency,
                        ],
                        [
                            'rate' => $rate,
                        ]
                    );

                    $this->info("Exchange rate updated: 1 USD = {$rate} {$currency}");
                } else {
                    $this->error("Failed to fetch rate for {$currency}: " . json_encode($data));
                }

            } catch (\Exception $e) {
                $this->error("Error fetching {$currency} rate: " . $e->getMessage());
            }
        }
    }
}
