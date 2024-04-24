<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class USPSController extends Controller
{
    public function create_token()
    {
        try {
            $token = Http::asForm()->post('https://apis.fedex.com/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => 'l78197839d6016410286b488993d0d9b87',
                'client_secret' => '399acd0e75624a6a8a8cf0f8fdb6917e'
            ]);

            $token=json_decode($token->body());

            return response()->json(['token' => $token]);
        } catch (\Exception $ex) {
            return response()->json([$ex->getMessage()]);
        }
    }
}
