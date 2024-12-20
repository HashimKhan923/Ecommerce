<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use Mail;

class EmailController extends Controller
{
    public function sent(Request $request)
    {
        $Order = Order::with('order_detail.products.product_gallery')->where('id',$request->order_id)->first();
        $Shop = Shop::where('id',$request->shop_id)->first();
        // $Customer = User::where('id',$request->customer_id)->first();

        Mail::send(
            'email.Order.order_seller_to_customer',
            [
                'buyer_name' => $request->customer_name,
                'shop' => $Shop,
                'order'=> $Order,
                'body' => $request->message
            ],
            function ($message) use ($request) { 
                $message->from('support@dragonautomart.com','Dragon Auto Mart');
                $message->to($request->customer_email);
                if($request->seller_email)
                {
                    $message->cc($request->seller_email);
                }
                $message->subject($request->subject);
            }
        );
    }

    public function seller_to_customer_query(Request $request)
    {
        $Seller = User::where('id',$request->seller_id)->first();
        $Customer = User::where('id',$request->customer_id)->first();

        Mail::send(
        'email.seller_to_customer_query',
        [
            'Customer'=>$Customer,
            'Seller'=>$Seller,
            'Msg'=>$request->message,
            
        ], 

        function ($message) use ($Customer,$request) {
            $message->from('support@dragonautomart.com','Dragon Auto Mart');
            $message->to($Customer->email);
            if($request->seller_email)
            {
                $message->cc($request->seller_email);
            }
            $message->subject($request->subject);
        });
    }
}
