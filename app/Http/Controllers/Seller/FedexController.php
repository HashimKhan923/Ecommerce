<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;


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
                'Authorization' => 'Bearer'.env('FEDEX_SECRET'),
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
}
