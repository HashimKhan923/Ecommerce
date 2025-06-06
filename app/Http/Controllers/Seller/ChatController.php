<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ProductGallery;
use App\Models\User;
use Mail;
use DB;
use App\Services\FirebaseService;


class ChatController extends Controller
{

    public function groups($seller_id)
    {
        $data = Chat::with('shop','customer')
        ->where('seller_id', $seller_id)
        ->get();
    
        return response()->json(['data' => $data]);    
    
    }

    public function index(Request $request)
    {
        Chat::where('seller_id', $request->seller_id)
        ->where('customer_id', $request->customer_id)
        ->where('shop_id', $request->shop_id)
        ->where('status','unread')
        ->update(['status' => 'read']);

        $data = Chat::with([
            'seller', 
            'customer', 
            'shop', 
            'product' => function ($query) {
                $query->select('id', 'name')
                      ->with('product_single_gallery');
            }, 
            'my_customer' => function($query) use ($request) {
                $query->where('seller_id', $request->seller_id)
                      ->with(['customer.time_line', 'orders']);
            }
        ])
        ->where('seller_id', $request->seller_id)
        ->where('customer_id', $request->customer_id)
        ->where('shop_id', $request->shop_id)
        ->get();
    
        return response()->json(['data' => $data]);
    }
    


    public function send(Request $request)
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

        $chat = Chat::with('shop')->where('id',$send->id)->first();

        $ProductName = Product::where('id',$request->product_id)->first();
        $Shop = Shop::where('id',$request->shop_id)->first();
        $ProductImage = ProductGallery::where('product_id',$request->product_id)->first();
        $Seller = User::where('id',$request->seller_id)->first();
        $Customer = User::where('id',$request->customer_id)->first();
        if($ProductName)
        {
            Mail::send(
                'email.seller_to_customer_query_with_product',
                [
                    // 'ProductName'=>$ProductName->name,
                    // 'ProductImage'=>$ProductImage->image,
                    // 'ShopName'=>$Shop->name,
                    // 'ShopImage'=>$Shop->logo,
                    'Customer'=>$Customer,
                    'Seller'=>$Seller,
                    'Msg'=>$request->message,
                    
                ], 

                function ($message) use ($Customer) {
                    $message->from('support@dragonautomart.com','Dragon Auto Mart');
                    $message->to($Customer->email);
                    $message->subject('Message');
                });
        }
        else
        {
            Mail::send(
                'email.seller_to_customer_query_with_shop',
                [
                    'ShopName'=>$Shop->name,
                    'ShopImage'=>$Shop->logo,
                    'Customer'=>$Customer,
                    'Seller'=>$Seller,
                    'Msg'=>$request->message,
                    
                ], 

                function ($message) use ($Customer) {
                    $message->from('support@dragonautomart.com','Dragon Auto Mart');
                    $message->to($Customer->email);
                    $message->subject('Message');
                });
        }
        if($Customer->device_token != null)
        {
            FirebaseService::sendNotification($Customer->device_token,'New Message',$request->message);

        }


        return response()->json(['message'=>'message sent successfully!','chat'=>$chat,200]);
    }
}
