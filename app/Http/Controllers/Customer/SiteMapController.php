<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class SiteMapController extends Controller
{
    public function product_ids()
    {
      $product_ids = Product::pluck('id');

      return response()->json(['product_ids'=> $product_ids]);
    }
}
