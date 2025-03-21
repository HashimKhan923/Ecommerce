<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use Carbon\Carbon;
use Mail;

class SubscriberController extends Controller
{
    public function create(Request $request)
    {
        $check = Subscriber::where('email',$request->email)->first();
        if($check)
        {
            $response = ['status'=>true,"message" => "You have Already Subscribed!"];
            return response($response, 200);
        }
        else
        {
            $new = new Subscriber();
            $new->email = $request->email;
            $new->date = Carbon::now('Asia/Karachi');
            $new->save();

            // Mail::send(
            //     'email.subscriber',
            //     [
                    
            //     ],
            //     function ($message) use ($request) { 
            //         $message->from('support@dragonautomart.com','Dragon Auto Mart');
            //         $message->to($request->email);
            //         $message->subject('Subscription Confirmation');
            //     }
            // );

            Mail::mailer('no_reply')->send(
                'email.subscriber',
                [
                    
                ],
                function ($message) use ($request) { 
                    $message->from('no-reply@dragonautomart.com','Dragon Auto Mart');
                    $message->to($request->email);
                    $message->subject('Subscription Confirmation');
                }
            );

    
            $response = ['status'=>true,"message" => "You have Subscribe Successfully!"];
            return response($response, 200);
        }

    }
}
