<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mail;
use App\Models\ProductGallery;
use App\Models\Product;
use App\Models\User;

class SellerContactController extends Controller
{
    public function send(Request $request)
    {


        $ProductName = Product::where('id',$request->product_id)->first();
        $ProductImage = ProductGallery::where('product_id',$request->product_id)->first();
        $Seller = User::where('id',$request->seller_id)->first();
        $Customer = User::where('id',$request->customer_id)->first();

        Mail::send(
            'email.customer_to_seller_query',
            [
                'ProductName'=>$ProductName->name,
                'ProductImage'=>$ProductImage->image,
                'Customer'=>$Customer,
                'Seller'=>$Seller,
                'Msg'=>$request->message,
                
            ], 
        
        function ($message) use ($Seller) {
            $message->from('support@dragonautomart.com','Dragon Auto Mart');
            $message->to($Seller->email);
            $message->subject('Customer Query');
        });

        return response()->json(['query sent successfully!',200]);
    }
}
