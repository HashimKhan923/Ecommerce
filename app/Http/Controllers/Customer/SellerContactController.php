<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mail;
use App\Models\ProductGallery;
use App\Models\Product;
use App\Models\User;
use App\Models\Shop;

class SellerContactController extends Controller
{
    public function send(Request $request)
    {

        
        $ProductName = Product::where('id',$request->product_id)->first();
        $Shop = Shop::where('id',$request->shop_id)->first();
        $ProductImage = ProductGallery::where('product_id',$request->product_id)->first();
        $Seller = User::where('id',$request->seller_id)->first();
        $Customer = User::where('id',$request->customer_id)->first();
        if($ProductName)
        {
            Mail::send(
                'email.customer_to_seller_query_with_product',
                [
                    'ProductName'=>$ProductName->name,
                    'ProductImage'=>$ProductImage->image,
                    'ShopName'=>$Shop->name,
                    'Customer'=>$Customer,
                    'Seller'=>$Seller,
                    'Msg'=>$request->message,
                    
                ], 

                function ($message) use ($Seller) {
                    $message->from('support@dragonautomart.com','Dragon Auto Mart');
                    $message->to($Seller->email);
                    $message->subject('Customer Query');
                });
        }
        else
        {
            Mail::send(
                'email.customer_to_seller_query_with_shop',
                [
                    'ShopName'=>$Shop->name,
                    'ShopImage'=>$Shop->logo,
                    'Customer'=>$Customer,
                    'Seller'=>$Seller,
                    'Msg'=>$request->message,
                    
                ], 

                function ($message) use ($Seller) {
                    $message->from('support@dragonautomart.com','Dragon Auto Mart');
                    $message->to($Seller->email);
                    $message->subject('Customer Query');
                });
        }



        


        return response()->json(['message'=>'query sent successfully!',200]);
    }
}
