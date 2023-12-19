<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Refund;

class StripeController extends Controller
{
    public function refund(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));


        $refund = Refund::create([
            'payment_intent' => $request->payment_intent_id,
            'amount' => $request->amount,
        ]);

    }
}
