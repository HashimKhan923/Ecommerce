<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;



class FedexController extends Controller
{
    public function show_rates(Request $request)
    {
        $url = 'https://apis-sandbox.fedex.com/rate/v1/rates/quotes';
        $token = $request->header('Authorization');

        $payload = [
            // "accountNumber" => [
            //     "value" => "XXXXX7364"
            // ],
            "requestedShipment" => [
                "shipper" => [
                    "address" => [
                        "postalCode" => "65247", // Ensure postal codes are strings
                        "countryCode" => "US"
                    ]
                ],
                "recipient" => [
                    "address" => [
                        "postalCode" => "75063", // Ensure postal codes are strings
                        "countryCode" => "US"
                    ]
                ],
                "pickupType" => "DROPOFF_AT_FEDEX_LOCATION",
                "rateRequestTypes" => [ // Correct key name for rate request types
                    "ACCOUNT",
                    "LIST"
                ],
                "requestedPackageLineItems" => [
                    [
                        "weight" => [
                            "units" => "LB",
                            "value" => 10
                        ]
                    ]
                ]
            ]
        ];


        $client = new Client();

        try {
        $response = $client->post($url, [
            'headers' => [
                'Authorization' => $token,
                'X-locale' => 'en_US',
                'Content-Type' => 'application/json',
            ],
            
            'json' => $payload, 
        ]);

        $body = $response->getBody()->getContents();

        return response()->json(json_decode($body));

        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function create_token()
    {
        try {
            $response = Http::asForm()->post('https://apis-sandbox.fedex.com/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => 'l7e0511d616c0b44aab043ae9e875dc078',
                'client_secret' => '1ec7e4d988744d6196aa5d1e86acad79'
            ]);

            $response=json_decode($response->body());
    
            return response()->json(['data' => $response]);
        } catch (\Exception $ex) {
            return response()->json([$ex->getMessage()]);
        }
    }
}
