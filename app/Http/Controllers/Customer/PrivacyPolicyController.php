<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PrivacyPolicy;
class PrivacyPolicyController extends Controller
{
    public function web_index()
    {
        $data = PrivacyPolicy::select('web_text')->first();

        return response()->json(['data'=>$data]);
    }

    public function app_index()
    {
        $data = PrivacyPolicy::select('app_text')->first();

        return response()->json(['data'=>$data]);
    }
}