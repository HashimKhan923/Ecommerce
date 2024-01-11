<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function payment(Request $request)
    {
        $token = $request->input('token');
        $amount = $request->input('amount');
        $name = $request->input('name');

        Stripe::setApiKey(config('services.stripe.secret'));

        try {



            $paymentIntent = PaymentIntent::create([
                'amount' => $amount * 100,
                'currency' => 'usd',
                'statement_descriptor_suffix' => 'Payment using Stripe',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'metadata' => [
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    // Add any other metadata you want to include
                ],
            ]);
    
            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ]);



        } catch (\Exception $e) {
            // Return a JSON response indicating failure (HTTP status 500)
            return response()->json(['success' => false, 'message' => 'Payment failed'], 500);
        }
    }
}