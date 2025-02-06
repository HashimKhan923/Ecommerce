<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportPolicy;

class SupportPolicyController extends Controller
{
    public function web_index()
    {
        $data = SupportPolicy::select('web_text')->first();

        return response()->json(['data'=>$data]);
    }

    public function app_index()
    {
        $data = SupportPolicy::select('app_text')->first();

        return response()->json(['data'=>$data]);
    }
}
