<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Disclaimer;

class DisclaimerController extends Controller
{
    public function index()
    {
        $data = Disclaimer::first();

        return response()->json(['data'=>$data]);
    }

    public function createOrUpdate(Request $request)
    {
        $data = Disclaimer::first();

        if($data == null)
        {
            $data = new Disclaimer();
        }
        $data->web_text = $request->web_text;
        $data->app_text = $request->app_text;
        $data->save();

        
        $response = ['status'=>true,"message" => "Disclaimer Saved Successfully!"];
        return response($response, 200);
    }
}