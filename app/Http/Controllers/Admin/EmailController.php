<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Mail;

class EmailController extends Controller
{

    public function sent(Request $request)
    {
        $seller = User::where('user_type','seller')->get();

        foreach($seller as $seller_id)
        {
            $Seller = User::where('id',$seller_id->id)->first();
            
            Mail::send(
                'email.admin.to_seller',
                [
                    'seller_name' => $Seller->name,
                    'body' => $request->message
                ],
                function ($message) use ($Seller, $request) { 
                    $message->from('support@dragonautomart.com','Dragon Auto Mart');
                    $message->to($Seller->email);
                    $message->subject($request->subject);
                }
            );
        }


    }
}
