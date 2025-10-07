<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class UPSTrackingService
{
    protected $baseUrl;
    protected $clientId;
    protected $clientSecret;

    public function __construct()
    {
        $this->baseUrl = 'https://onlinetools.ups.com';
        $this->clientId = env('UPS_CLIENT_ID');
        $this->clientSecret = env('UPS_CLIENT_SECRET');
    }

    /**
     * Get Access Token
     */
protected function getAccessToken()
{
    $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
        ->asForm()
        ->post("{$this->baseUrl}/security/v1/oauth/token", [
            'grant_type' => 'client_credentials',
        ]);

    if ($response->successful()) {
        return $response->json()['access_token'];
    }

    \Log::error('UPS OAuth token request failed', [
        'body' => $response->body(),
    ]);

    throw new \Exception('Failed to get UPS access token');
}

    /**
     * Track a UPS Shipment
     */
    public function trackShipment($trackingNumber)
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'transId' => uniqid(),
                'transactionSrc' => 'DragonAutoMart',
            ])
            ->get("{$this->baseUrl}/api/track/v1/details/{$trackingNumber}");

        if ($response->successful()) {
            return $response->json();
        } else {
            \Log::error('UPS Tracking API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'error' => true,
                'status' => $response->status(),
                'body' => $response->body(),
            ];
        }
    }
}
