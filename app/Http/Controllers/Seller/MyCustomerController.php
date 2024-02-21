<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MyCustomer;

class MyCustomerController extends Controller
{
    public function index($seller_id)
    {
      $data = MyCustomer::with('customer')->where('seller_id',$seller_id)->get();

      return response()->json(['data'=>$data]);
    }
}
