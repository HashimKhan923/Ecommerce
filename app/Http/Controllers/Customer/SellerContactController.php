<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SellerContact;
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

        Mail::send(
            'email.subscriber',
            [
                
            ],
            function ($message) use ($request) { 
                $message->from('support@dragonautomart.com','Dragon Auto Mart');
                $message->to($request->email);
                $message->subject('Subscription Confirmation');
            }
        );


        return response()->json(['message'=>'query sent successfully!',200]);
    }
}
