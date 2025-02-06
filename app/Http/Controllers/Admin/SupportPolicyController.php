<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportPolicy;

class SupportPolicyController extends Controller
{
    public function index()
    {
        $data = SupportPolicy::first();

        return response()->json(['data'=>$data]);
    }

    public function createOrUpdate(Request $request)
    {
        $data = SupportPolicy::first();

        if($data == null)
        {
            $data = new SupportPolicy();
        }
        $data->web_text = $request->web_text;
        $data->app_text = $request->app_text;
        $data->save();

        
        $response = ['status'=>true,"message" => "Support Policy Saved Successfully!"];
        return response($response, 200);
    }
}
