<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class USPSController extends Controller
{
    public function create_token()
    {
        try {
            $token = Http::asForm()->post('https://api.usps.com/oauth2/v3/token', [
                'grant_type' => 'client_credentials',
                'client_id' => '8iWRbtGUFL7siyzI3iAAGEp9N4u821nn',
                'client_secret' => 'SzyDsK4UVjZnUxfk'
            ]);

            $token=json_decode($token->body());

            return response()->json(['token' => $token]);
        } catch (\Exception $ex) {
            return response()->json([$ex->getMessage()]);
        }
    }

    public function show_rates(Request $request)
    {

        $url = 'https://api.usps.com/prices/v3/base-rates/search';
        $token = $request->header('Authorization');

      $payload = [
            "originZIPCode"=> $request->shipper_postalCode,
            "destinationZIPCode"=> $request->recipient_postalCode,
            "weight"=> $request->weight,
            "length"=> $request->length,
            "width"=> $request->width,
            "height"=> $request->height,
            "mailClass"=> "PRIORITY_MAIL",
            "processingCategory"=> "LETTERS",
            "rateIndicator"=> "3D",
            "destinationEntryFacilityType"=> "NONE",
            "priceType"=> "RETAIL",

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
}
