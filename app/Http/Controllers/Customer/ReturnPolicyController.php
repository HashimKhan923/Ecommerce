<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReturnPolicy;

class ReturnPolicyController extends Controller
{
    public function web_index()
    {
        $data = ReturnPolicy::select('web_text')->first();

        return response()->json(['data'=>$data]);
    }

    public function app_index()
    {
        $data = ReturnPolicy::select('app_text')->first();

        return response()->json(['data'=>$data]);
    }
}
