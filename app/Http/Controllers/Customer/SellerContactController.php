<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SellerContact;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ProductGallery;
use App\Models\User;
use Mail;


class SellerContactController extends Controller
{
    public function send(Request $request)
    {

        $send = new SellerContact();
        $send->product_id = $request->product_id;
        $send->shop_id = $request->shop_id;
        $send->seller_id = $request->seller_id;
        $send->customer_id = $request->customer_id;
        $send->message = $request->message;
        $send->save();

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
                    'ShopImage'=>$Shop->logo,
                    'Customer'=>$Customer,
                    'Seller'=>$Seller,
                    'Msg'=>$request->message,
                    
                ], 

                function ($message) use ($Seller) {
                    $message->from('support@dragonautomart.com','Dragon Auto Mart');
                    $message->to($Seller->email);
                    $message->subject('Message');
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
                    $message->subject('Message');
                });
        }


        return response()->json(['message'=>'query sent successfully!',200]);
    }
}
