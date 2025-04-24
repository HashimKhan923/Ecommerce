<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FedExTrackingService
{
    protected $baseUrl;
    protected $apiKey;
    protected $apiSecret;
    protected $accountNumber;

    public function __construct()
    {
        $this->baseUrl = 'https://apis.fedex.com';
        $this->apiKey = env('FEDEX_KEY');
        $this->apiSecret = env('FEDEX_SECRET');
        $this->accountNumber = env('FEDEX_ACCOUNT');
    }

    public function trackShipment($trackingNumber)
    {
        $token = $this->getAccessToken();

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->post("{$this->baseUrl}/track/v1/trackingnumbers", [
            "trackingInfo" => [
                [
                    "trackingNumberInfo" => [
                        "trackingNumber" => $trackingNumber,
                    ],
                ],
            ],
            "includeDetailedScans" => true,
        ]);

        if ($response->successful()) {
            return $response->json();
        } else {
            \Log::error('FedEx tracking API error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        
            // Optionally throw or return an error structure
            return [
                'error' => true,
                'status' => $response->status(),
                'body' => $response->body(),
            ];
        }

        logger()->error('FedEx tracking failed', [
            'trackingNumber' => $trackingNumber,
            'response' => $response->json(),
        ]);

        return null;
    }

    protected function getAccessToken()
    {
        $response = Http::asForm()->post('https://apis.fedex.com/oauth/token', [
            'grant_type' => 'client_credentials',
            'client_id' => env('FEDEX_TRACKING_KEY'),
            'client_secret' => env('FEDEX_TRACKING_SECRET')
        ]);

        if ($response->successful()) {
            return $response['access_token'];
        }

        logger()->error('FedEx token request failed', [
            'response' => $response->json(),
        ]);

        throw new \Exception('Unable to fetch FedEx access token');
    }
}
