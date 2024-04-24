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
}
