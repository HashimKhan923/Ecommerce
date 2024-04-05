<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;



class FedexController extends Controller
{

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

    public function show_rates(Request $request)
    {
        $url = 'https://apis-sandbox.fedex.com/rate/v1/rates/quotes';
        $token = $request->header('Authorization');

        $requestedPackageLineItems = [];

        foreach ($request->requestedPackageLineItems as $packageItem) {
            $requestedPackageLineItems[] = [
                "weight" => [
                    "value" => $packageItem['weight'],
                    "units" => "LB"
                ],
                "dimensions"=> [
                    "length"=> $packageItem['length'],
                    "width"=> $packageItem['width'],
                    "height"=> $packageItem['height'],
                    "units"=> "IN"
                ]
            ];
        }

        $payload = [
            "accountNumber" => [
                "value" => "740561073"
            ],
            "requestedShipment" => [
                "shipper" => [
                    "address" => [
                        "postalCode" => $request->shipper_postalCode, 
                        "countryCode" => $request->shipper_countryCode
                    ]
                ],
                "recipient" => [
                    "address" => [
                        "postalCode" => $request->recipient_postalCode, 
                        "countryCode" => $request->recipient_countryCode
                    ]
                ],
                "pickupType" => "DROPOFF_AT_FEDEX_LOCATION",
                "rateRequestType" => [ 
                    "ACCOUNT",
                    "LIST"
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

        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function create_shipment(Request $request)
    {

        $url = 'https://apis-sandbox.fedex.com/ship/v1/shipments';
        $token = $request->header('Authorization');

        $requestedPackageLineItems = [];

        foreach ($request->requestedPackageLineItems as $packageItem) {
            $requestedPackageLineItems[] = [
                "weight" => [
                    "value" => $packageItem['weight'],
                    "units" => "LB"
                ],
                "dimensions"=> [
                    "length"=> $packageItem['length'],
                    "width"=> $packageItem['width'],
                    "height"=> $packageItem['height'],
                    "units"=> "IN"
                ]
            ];
        }
        
        $payload = [
            "labelResponseOptions" => "URL_ONLY",
            "requestedShipment" => [
                "shipper" => [
                    "contact" => [
                        "personName" => $request->shipper_name,
                        "phoneNumber" => $request->shipper_phone_number,
                        "companyName" => $request->shipper_company_name
                    ],
                    "address" => [
                        "streetLines" => [
                            $request->shipper_street_address
                        ],
                        "city" => $request->shipper_city,
                        "stateOrProvinceCode" => $request->shipper_province_code,
                        "postalCode" => $request->shipper_postal_code,
                        "countryCode" => $request->shipper_country_code
                    ]
                ],
                "recipients" => [
                    [
                        "contact" => [
                            "personName" => $request->customer_name,
                            "phoneNumber" => $request->customer_phone_number,
                        ],
                        "address" => [
                            "streetLines" => [
                                $request->customer_street_address
                            ],
                            "city" => $request->customer_city,
                            "stateOrProvinceCode" => $request->customer_province_code,
                            "postalCode" => $request->customer_postal_code,
                            "countryCode" => $request->customer_country_code
                        ]
                    ]
                ],
                "shipDatestamp" => $request->ship_date,
                "serviceType" => $request->service_type,
                "packagingType" => "YOUR_PACKAGING",
                "pickupType" => "USE_SCHEDULED_PICKUP",
                "blockInsightVisibility" => false,
                "shippingChargesPayment" => [
                    "paymentType" => "SENDER"
                ],
                "labelSpecification" => [
                    "imageType" => "PDF",
                    "labelStockType" => "PAPER_85X11_TOP_HALF_LABEL"
                ],
                "requestedPackageLineItems" => $requestedPackageLineItems
            ],
            "accountNumber" => [
                "value" => "740561073"
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


    public function cancel_shipment(Request $request)
    {
        $url = 'https://apis-sandbox.fedex.com/ship/v1/shipments/cancel';
        $token = $request->header('Authorization');

        $payload = [
            "accountNumber" => [
                "value" => "740561073"
            ],
            "emailShipment"=> "false",
            "senderCountryCode"=>"US",
            "deletionControl"=>"DELETE_ALL_PACKAGES",
            "trackingNumber"=> $request->trackingNumber
        ];

        
        $client = new Client();

        try {
        $response = $client->put($url, [
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


}
