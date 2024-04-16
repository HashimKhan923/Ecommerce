<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductListingPayment;
use App\Models\FeaturedProductOrder;

class BillingController extends Controller
{
    public function index($seller_id)
    {
        $ProductListingPayment = ProductListingPayment::where('seller_id',$seller_id)->get();
        $FeaturedProductOrder = FeaturedProductOrder::with('product.product_gallery')->where('seller_id',$seller_id)->get();

        return response()->json(['ProductListingPayment'=>$ProductListingPayment,'FeaturedProductOrder'=>$FeaturedProductOrder]);
    }
}
