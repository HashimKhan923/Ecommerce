<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ProductGallery;
use App\Models\User;
use Mail;
use DB;

class ChatController extends Controller
{

    public function query(Request $request)
    {
        $send = new Chat();
        $send->product_id = $request->product_id;
        $send->shop_id = $request->shop_id;
        $send->customer_id = $request->customer_id;
        $send->seller_id = $request->seller_id;
        $send->message = $request->message;
        $send->sender_id = $request->sender_id;
        $send->reciver_id = $request->reciver_id;
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


        return response()->json(['message'=>'query sent successfully!','chat'=>$send,200]);
    }


    public function groups($customer_id)
    {

        $data = Chat::with('shop')
        ->where('customer_id', $customer_id)
        ->get();
    
        return response()->json(['data' => $data]);
    }

    public function index(Request $request)
    {

        Chat::where('seller_id',$request->seller_id)
        ->where('customer_id',$request->customer_id)
        ->where('shop_id',$request->shop_id)
        ->where('status','unread')
        ->update(['status' => 'read']);



        $data = Chat::with([
            'shop',
            'seller',
            'customer',
            'product' => function ($query) {
                $query->select('id', 'name')
                      ->with('product_single_gallery');
            },
        ])
        ->where('seller_id', $request->seller_id)
        ->where('customer_id', $request->customer_id)
        ->where('shop_id', $request->shop_id)
        ->get();
        
                
        return response()->json(['data'=>$data]);
    }

    public function send(Request $request)
    {

        $send = new Chat();
        $send->customer_id = $request->customer_id;
        $send->seller_id = $request->seller_id;
        $send->shop_id = $request->shop_id;
        $send->message = $request->message;
        $send->sender_id = $request->sender_id;
        $send->reciver_id = $request->reciver_id;
        $send->save();




        return response()->json(['message'=>'message sent successfully!','chat'=>$send,200]);
    }
}
