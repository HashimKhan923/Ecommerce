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

        $payload = [
            "requestedShipment" => [
                "shipper" => [
                    "address" => [
                        "postalCode" => "Y99801",
                        "countryCode" => "US"
                    ]
                ],
                "recipient" => [
                    "address" => [
                        "postalCode" => "99501",
                        "countryCode" => "US"
                    ]
                ],
                "packageCount" => 1, // Number of packages
                "requestedPackageLineItems" => [
                    [
                        "weight" => [
                            "units" => "LB", // Weight units (e.g., LB for pounds)
                            "value" => 10 // Weight value
                        ],
                        "dimensions" => [
                            "length" => 10, // Length of the package
                            "width" => 8, // Width of the package
                            "height" => 6, // Height of the package
                            "units" => "IN" // Dimension units (e.g., IN for inches)
                        ]
                    ]
                ],
                "shippingChargesPayment" => [
                    "paymentType" => "SENDER" // Payment type (e.g., SENDER, RECIPIENT, THIRD_PARTY)
                ],
                "rateRequestTypes" => [
                    "ACCOUNT", // Account rate request type
                    "LIST" // List rate request type
                ],
                "packageDetail" => "INDIVIDUAL_PACKAGES", // Package detail (e.g., INDIVIDUAL_PACKAGES, PACKAGE_GROUPS)
                "preferredCurrency" => "USD" 
            ]
        ];


        $client = new Client();

        try {
        $response = $client->post($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . env('FEDEX_KEY'),
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

    public function create_token(Request $request)
    {
        try {
            $input = $request->input(); 
    
            $response = Http::post('https://apis-sandbox.fedex.com/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => 'l7e0511d616c0b44aab043ae9e875dc078',
                'client_secret' => '1ec7e4d988744d6196aa5d1e86acad79'
            ]);
        
            return response()->json(['data' => $response->body()]);
        } catch (\Exception $ex) {
            return response()->json([$ex->getMessage()]);
        }
    }
}
