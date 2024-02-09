<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FandQ;
use App\Models\SellerFandQ;


class FandQController extends Controller
{
    public function index()
    {
        $data = FandQ::all();

        return response()->json(['data'=>$data]);
    }

    public function index2()
    {
        $data = SellerFandQ::all();

        return response()->json(['data'=>$data]);
    }
}
