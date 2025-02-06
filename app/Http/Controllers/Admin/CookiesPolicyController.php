<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CookiesPolicy;

class CookiesPolicyController extends Controller
{
    public function index()
    {
        $data = CookiesPolicy::first();

        return response()->json(['data'=>$data]);
    }

    public function createOrUpdate(Request $request)
    {
        $data = CookiesPolicy::first();

        if($data == null)
        {
            $data = new CookiesPolicy();
        }
        $data->web_text = $request->web_text;
        $data->app_text = $request->app_text;
        $data->save();

        
        $response = ['status'=>true,"message" => "Cookies Policy Saved Successfully!"];
        return response($response, 200);
    }
}
