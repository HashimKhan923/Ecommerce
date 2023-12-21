<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReturnPolicy;

class ReturnPolicyController extends Controller
{
    public function index()
    {
        $data = ReturnPolicy::first();

        return response()->json(['data'=>$data]);
    }

    public function createOrUpdate(Request $request)
    {
        $data = ReturnPolicy::first();

        if($data == null)
        {
            $data = new ReturnPolicy();
        }
        $data->web_text = $request->web_text;
        $data->app_text = $request->app_text;
        $data->save();

        
        $response = ['status'=>true,"message" => "Saved Successfully!"];
        return response($response, 200);
    }
}
