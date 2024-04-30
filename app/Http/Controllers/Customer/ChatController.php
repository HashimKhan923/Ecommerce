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


    public function groups($customer_id)
    {
        // $data = Chat::with('seller', 'customer', 'shop')
        // ->select('chats.*') 
        // ->join(
        //     DB::raw('(SELECT seller_id FROM chats WHERE customer_id = ' . $customer_id . ' GROUP BY seller_id) as sub'),
        //     'chats.seller_id',
        //     '=',
        //     'sub.seller_id'
        // )
        // ->groupBy('chats.seller_id', 'chats.id') 
        // ->get();


        $uniqueCountries = Chat::with('seller', 'customer', 'shop')->join(
            Chat::select('seller_id')->distinct()->getQuery(),
            'chats.seller_id',
            '=',
            'subquery.seller_id'
        )->where('customer_id',$customer_id)->get(['chats.*']);
    
        return response()->json(['data' => $data]);
    }

    public function index(Request $request)
    {
        $data=Chat::wherewith('seller','customer')->where('seller_id',$request->seller_id)->where('customer_id',$request->customer_id)->get();
        
        return response()->json(['data'=>$data]);
    }

    public function send(Request $request)
    {

        $send = new Chat();
        $send->product_id = $request->product_id;
        $send->shop_id = $request->shop_id;
        $send->customer_id = $request->customer_id;
        $send->seller_id = $request->seller_id;
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
