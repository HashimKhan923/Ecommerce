<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Mail;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;


class PaymentController extends Controller
{

    public function check_payment_method($id)
    {
        try {
            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
            $data = $stripe->paymentMethods->retrieve($id, []);
            
            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function stripe_payment(Request $request)
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
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function paypal_payment(Request $request)
    {
        $amount = $request->input('amount');

        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                config('services.paypal.client_id'),
                config('services.paypal.secret')
            )
        );

        try {
            $payer = new Payer();
            $payer->setPaymentMethod('paypal');

            $amountObj = new Amount();
            $amountObj->setCurrency('USD')
                ->setTotal($amount);

            $transaction = new Transaction();
            $transaction->setAmount($amountObj)
                ->setDescription('Payment using PayPal');

            $redirectUrls = new RedirectUrls();
            $redirectUrls->setReturnUrl(url('/api/paypal/success'))
                ->setCancelUrl(url('/api/paypal/cancel'));

            $payment = new Payment();
            $payment->setIntent('sale')
                ->setPayer($payer)
                ->setTransactions([$transaction])
                ->setRedirectUrls($redirectUrls);

            $payment->create($apiContext);

            $approvalUrl = $payment->getApprovalLink();

            return response()->json([
                'approvalUrl' => $approvalUrl,
            ]);

        } catch (\Exception $e) {

            Mail::send(
                'email.exception',
                [
                    'exceptionMessage' => $e->getMessage(),
                    'exceptionFile' => $e->getFile(),
                    'exceptionLine' => $e->getLine(),
                ],
                function ($message) {
                    $message->from('support@dragonautomart.com', 'Dragon Auto Mart');
                    $message->to('support@dragonautomart.com'); // Send to support email
                    $message->subject('Dragon Exception');
                }
            );

            // Return a JSON response indicating failure (HTTP status 500)
            return response()->json(['success' => false, 'message' => 'Payment failed'], 500);
        }
    }


    public function paypalSuccess(Request $request)
    {


        return response()->json(['message'=>'Payment Successfull.','data'=>$request,200]);
    }

    }
