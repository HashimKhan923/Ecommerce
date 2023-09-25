<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TermCondition;

class TermConditionController extends Controller
{
    public function index()
    {
        $data = TermCondition::first();

        return response()->json(['data'=>$data]);
    }

    public function createOrUpdate(Request $request)
    {
        $data = TermCondition::first();

        if($data == null)
        {
            $data = new TermCondition();
        }
        $data->web_text = $request->web_text;
        $data->app_text = $request->app_text;
        $data->save();

        
        $response = ['status'=>true,"message" => "Terms & Conditions Saved Successfully!"];
        return response($response, 200);
    }
}

