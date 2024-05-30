<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class EmailController extends Controller
{
    public function sent(Request $request)
    {
        foreach($request->ids as $seller_id)
        {
            $Seller = User::where('id',$seller_id)->first();
            Mail::send(
                'email.admin.to_seller',
                [
                    'seller_name' => $Seller->name,
                    'body' => $request->message
                ],
                function ($message) use ($Customer, $request) { 
                    $message->from('support@dragonautomart.com','Dragon Auto Mart');
                    $message->to($Seller->email);
                    $message->subject($request->subject);
                }
            );
        }


    }
}
