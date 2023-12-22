<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FandQ;


class FandQController extends Controller
{
    public function index()
    {
        $data = FandQ::all();

        return response()->json(['data'=>$data]);
    }
}
