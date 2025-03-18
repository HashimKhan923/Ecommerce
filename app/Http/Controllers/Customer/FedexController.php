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

            // $trackingToken = Http::asForm()->post('https://apis.fedex.com/oauth/token', [
            //     'grant_type' => 'client_credentials',
            //     'client_id' => env('FEDEX_TRACKING_KEY'),
            //     'client_secret' => env('FEDEX_TRACKING_SECRET')

            // ]);

            // $trackingToken=json_decode($trackingToken->body());
    
            return response()->json(['token' => $token]);
        } catch (\Exception $e) {

            // Mail::send(
            //     'email.exception',
            //     [
            //         'exceptionMessage' => $e->getMessage(),
            //         'exceptionFile' => $e->getFile(),
            //         'exceptionLine' => $e->getLine(),
            //     ],
            //     function ($message) {
            //         $message->from('support@dragonautomart.com', 'Dragon Auto Mart');
            //         $message->to('support@dragonautomart.com'); // Send to support email
            //         $message->subject('Dragon Exception');
            //     }
            // );

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
                    ]
                ],
                "pickupType" => "DROPOFF_AT_FEDEX_LOCATION",
                "serviceType" => [
                    "INTERNATIONAL_ECONOMY"
                ],
                "rateRequestType" => [ 
                    "ACCOUNT",
                    "LIST"
                ],
                "customsClearanceDetail" => [
                    "customsValue" => [
                        "amount" => $request->declared_value,
                        "currency" => "USD"
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




}
