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
        $data2 = SellerFandQ::all();

        return response()->json(['data'=>$data,'data2'=>$data2]);
    }

    public function index2()
    {
        $data = SellerFandQ::all();

        return response()->json(['data'=>$data]);
    }
}
