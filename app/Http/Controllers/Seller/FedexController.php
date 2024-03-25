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
                'Authorization' => 'Bearer ' . 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzY29wZSI6WyJDWFMtVFAiXSwiUGF5bG9hZCI6eyJjbGllbnRJZGVudGl0eSI6eyJjbGllbnRLZXkiOiJsN2UwNTExZDYxNmMwYjQ0YWFiMDQzYWU5ZTg3NWRjMDc4In0sImF1dGhlbnRpY2F0aW9uUmVhbG0iOiJDTUFDIiwiYWRkaXRpb25hbElkZW50aXR5Ijp7InRpbWVTdGFtcCI6IjI1LU1hci0yMDI0IDA1OjU5OjI0IEVTVCIsImdyYW50X3R5cGUiOiJjbGllbnRfY3JlZGVudGlhbHMiLCJhcGltb2RlIjoiU2FuZGJveCIsImN4c0lzcyI6Imh0dHBzOi8vY3hzYXV0aHNlcnZlci1zdGFnaW5nLmFwcC5wYWFzLmZlZGV4LmNvbS90b2tlbi9vYXV0aDIifSwicGVyc29uYVR5cGUiOiJEaXJlY3RJbnRlZ3JhdG9yX0IyQiJ9LCJleHAiOjE3MTEzNjc5NjQsImp0aSI6IjRjOGYwNGZlLTJhMjMtNGM2OS05ZmU2LTE2N2FiZDQ0YTc3MiJ9.G2s3UAaaftlx9t-gNycnbA1kUaShBaHHgGXFiJ2GDj1gKLyv8ajqY3WGAM4A6WHvE6yYWM_vby9noBy2PpoUTAmywm1ayK4902MRsj7ErXXecpD7OJNoWCIcD3ct8Fx_-D7venUNehd9gFfT_hoVcsFq9ZYHOypYEvat8wHfj-PaWDlaueAIeSjI5OI2vPVfRF62mQ4WDDh0_sWEqPNR9Jj3G13wlcbh_rzx7bN1TZ0y-j0ADmKdCo8i8_aHOhbHQTjOu2SZH2G6Fy-WXfk5p0apHmz0U7tyLGtUHQo1KzKAKx_7LCfK6lZ_nQz_sFoUQRzYHTLafR1cImH_z4B4XWWUilwla8jNJx6AoZ2K9w7mLKWOXrwLSVsbUMXzhws3C5Nw1Wzrdu6R9HTv3Z9ctiqsd0I1RKDNoFXtga4cGrkP-9obaa3oHBAEa1w-ooFQB2yn8L42DkMMOLS1PfN-ZxA6JMKRb4xmPj44cfUT4WesqEjx0p_JE6UoBc9GQ9NxKgBT4869rPhKOJH3wi3R4scxoBAMbgZdMmUvCnky473GasFkahnt__M4IZqMDIRnihJt8_ubyqR3AnISB7fGi0Q53dzz4dOnNyJ9JBohkSF1xttGoBEigfeKM7J8KUCqo50WZAtIj5OrAz1nxhvY7IDlpxnEWZ1pbInLjbhEK2w',
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
