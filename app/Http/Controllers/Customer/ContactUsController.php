<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactUs;
use Mail;

class ContactUsController extends Controller
{
    public function create(Request $request)
    {
        $new = new ContactUs();
        $new->email = $request->email;
        $new->subject = $request->subject;
        $new->message = $request->message;
        $new->save();

        Mail::send(
            'email.contactus',
            [
                'email' => $request->name,
                'subject' => $request->subject,
                'message1' => $request->message,
            ],
            function ($message) use ($request) { // Add $user variable here
                $message->from('support@dragonautomart.com','Dragon Auto Mart');
                $message->to('support@dragonautomart.com');
                $message->subject('Contact');
            }
        );


    }
}
