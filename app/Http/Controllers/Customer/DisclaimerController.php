<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Disclaimer;
class DisclaimerController extends Controller
{
    public function web_index()
    {
        $data = Disclaimer::select('web_text')->first();

        return response()->json(['data'=>$data]);
    }

    public function app_index()
    {
        $data = Disclaimer::select('app_text')->first();

        return response()->json(['data'=>$data]);
    }

}