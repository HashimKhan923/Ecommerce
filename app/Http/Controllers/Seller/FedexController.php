<?php

namespace App\Http\Controllers\Seller;

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

            $trackingToken = Http::asForm()->post('https://apis.fedex.com/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => env('FEDEX_TRACKING_KEY'),
                'client_secret' => env('FEDEX_TRACKING_SECRET')

            ]);

            $trackingToken=json_decode($trackingToken->body());
    
            return response()->json(['token' => $token,'trackingToken' => $trackingToken]);
        } catch (\Exception $e) {

            Mail::send(
                'email.exception',
                [
                    'exceptionMessage' => $e->getMessage(),
                    'exceptionFile' => $e->getFile(),
                    'exceptionLine' => $e->getLine(),
                ],
                function ($message) {
                    $message->from('support@dragonautomart.com', 'Dragon Auto Mart');
                    $message->to('support@dragonautomart.com'); // Send to support email
                    $message->subject('Dragon Exception');
                }
            );

            return response()->json([$e->getMessage()]);
        }
    }

    public function show_rates(Request $request)
    {
        $url = 'https://apis.fedex.com/rate/v1/rates/quotes';
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
                "value" => env('FEDEX_ACCOUNT')
    
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
                        "countryCode" => $request->recipient_countryCode,
                        "residential"=> $request->is_residential
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

        } catch (\Exception $e) {

            Mail::send(
                'email.exception',
                [
                    'exceptionMessage' => $e->getMessage(),
                    'exceptionFile' => $e->getFile(),
                    'exceptionLine' => $e->getLine(),
                ],
                function ($message) {
                    $message->from('support@dragonautomart.com', 'Dragon Auto Mart');
                    $message->to('support@dragonautomart.com'); // Send to support email
                    $message->subject('Dragon Exception');
                }
            );

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function create_shipment(Request $request)
    {

        $url = 'https://apis.fedex.com/ship/v1/shipments';
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
                    "labelStockType" => "PAPER_4X6"
                ],
                "requestedPackageLineItems" => $requestedPackageLineItems
            ],
            "accountNumber" => [
                "value" => env('FEDEX_ACCOUNT')
                // "value" => "740561073"
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

            Mail::send(
                'email.exception',
                [
                    'exceptionMessage' => $e->getMessage(),
                    'exceptionFile' => $e->getFile(),
                    'exceptionLine' => $e->getLine(),
                ],
                function ($message) {
                    $message->from('support@dragonautomart.com', 'Dragon Auto Mart');
                    $message->to('support@dragonautomart.com'); // Send to support email
                    $message->subject('Dragon Exception');
                }
            );

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function cancel_shipment(Request $request)
    {
        $url = 'https://apis.fedex.com/ship/v1/shipments/cancel';
        $token = $request->header('Authorization');

        $payload = [
            "accountNumber" => [
                "value" => env('FEDEX_ACCOUNT')
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

        } catch (\Exception $e) {

            Mail::send(
                'email.exception',
                [
                    'exceptionMessage' => $e->getMessage(),
                    'exceptionFile' => $e->getFile(),
                    'exceptionLine' => $e->getLine(),
                ],
                function ($message) {
                    $message->from('support@dragonautomart.com', 'Dragon Auto Mart');
                    $message->to('support@dragonautomart.com'); // Send to support email
                    $message->subject('Dragon Exception');
                }
            );


            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function track_shipment(Request $request)
    {
        $url = 'https://apis.fedex.com/track/v1/associatedshipments';
        $token = $request->header('Authorization');
    
        $payload = [
            "masterTrackingNumberInfo" => [
                "trackingNumberInfo" => [
                    "trackingNumber" => $request->tracking_number
                ]
            ],
            "associatedType" => "STANDARD_MPS"
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

            Mail::send(
                'email.exception',
                [
                    'exceptionMessage' => $e->getMessage(),
                    'exceptionFile' => $e->getFile(),
                    'exceptionLine' => $e->getLine(),
                ],
                function ($message) {
                    $message->from('support@dragonautomart.com', 'Dragon Auto Mart');
                    $message->to('support@dragonautomart.com'); // Send to support email
                    $message->subject('Dragon Exception');
                }
            );


            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function update_shipping_amount(Request $request)
    {
        $order = Order::where('id',$request->order_id)->first();
        $order->shipping_amount = $request->shipping_amount;
        $order->save();

        return response()->json(['status'=>true]);

    }


}
