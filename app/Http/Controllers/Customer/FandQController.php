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
        $data = FandQ::orderBy('order', 'asc')->get();
        $data2 = SellerFandQ::orderBy('order', 'asc')->get();

        return response()->json(['data'=>$data,'data2'=>$data2]);
    }

    public function index2()
    {
        $data = SellerFandQ::orderBy('order', 'asc')->get();

        return response()->json(['data'=>$data]);
    }
}
