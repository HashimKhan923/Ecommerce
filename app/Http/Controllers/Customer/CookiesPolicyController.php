<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CookiesPolicy;

class CookiesPolicyController extends Controller
{
    public function web_index()
    {
        $data = CookiesPolicy::select('web_text')->first();

        return response()->json(['data'=>$data]);
    }

    public function app_index()
    {
        $data = CookiesPolicy::select('app_text')->first();

        return response()->json(['data'=>$data]);
    }
}
