<?php

namespace App\Http\Controllers\Customer;

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
}
