<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use App\Models\Order;
use Mail;



class FedexController extends Controller
{

    public function create_token()
    {
        try {
            $token = Http::asForm()->post('https://apis.fedex.com/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => env('FEDEX_KEY'),
                'client_secret' => env('FEDEX_SECRET')

            ]);

            $token=json_decode($token->body());


    
            return response()->json(['token' => $token]);
        } catch (\Exception $e) {


            return response()->json([$e->getMessage()]);
        }
    }

public function show_rates(Request $request)
{
    $url = 'https://apis.fedex.com/rate/v1/rates/quotes';
    $token = $request->header('Authorization');

    $requestedPackageLineItems = [];

    foreach ($request->requestedPackageLineItems as $packageItem) {
        $packageData = [
            "weight" => [
                "value" => $packageItem['weight'],
                "units" => $packageItem['units'] ?? "KG" // Middle East prefers KG
            ]
        ];

        if (!empty($packageItem['length']) && !empty($packageItem['width']) && !empty($packageItem['height'])) {
            $packageData["dimensions"] = [
                "length" => $packageItem['length'],
                "width" => $packageItem['width'],
                "height" => $packageItem['height'],
                "units" => $packageItem['dimension_unit'] ?? "CM"
            ];
        }

        $requestedPackageLineItems[] = $packageData;
    }

    $payload = [
        "accountNumber" => [
            "value" => env('FEDEX_ACCOUNT')
        ],
        "requestedShipment" => [
            "shipper" => [
                "address" => [
                    "streetLines" => [$request->shipper_street ?? "Unknown"],
                    "city" => $request->shipper_city ?? "",
                    "postalCode" => $request->shipper_postalCode ?? "00000",
                    "countryCode" => $request->shipper_countryCode
                ]
            ],
            "recipient" => [
                "address" => [
                    "streetLines" => [$request->recipient_street ?? "Unknown"],
                    "city" => $request->recipient_city ?? "",
                    "postalCode" => $request->recipient_postalCode ?? "00000",
                    "countryCode" => $request->recipient_countryCode,
                    "residential" => false
                ]
            ],
            "pickupType" => "DROPOFF_AT_FEDEX_LOCATION",
            "serviceType" => $request->serviceType ?? "INTERNATIONAL_PRIORITY",
            "rateRequestType" => ["ACCOUNT"],
            "customsClearanceDetail" => [
                "commodities" => [
                    [
                        "description" => $request->commodity_description ?? "Car Parts",
                        "quantity" => $request->commodity_quantity ?? 1,
                        "quantityUnits" => "PCS",
                        "weight" => [
                            "value" => $request->commodity_weight ?? 1,
                            "units" => "KG"
                        ],
                        "customsValue" => [
                            "amount" => $request->declared_value,
                            "currency" => $request->currency ?? "USD"
                        ]
                    ]
                ],
                "customsValue" => [
                    "amount" => $request->declared_value,
                    "currency" => $request->currency ?? "USD"
                ]
            ],
            "requestedPackageLineItems" => $requestedPackageLineItems
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
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}





}
