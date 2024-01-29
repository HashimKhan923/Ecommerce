<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Refund;
use App\Models\Order;
use App\Models\User;
use App\Models\Payout;
use App\Models\Notification;
use Mail;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Refund as PaypalRefund;
use PayPal\Api\Sale;
use PayPal\Api\Amount;
use PayPal\Exception\PayPalException;

class PaypalController extends Controller
{
    protected $apiContext;

    public function __construct()
    {
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                config('services.paypal.client_id'),
                config('services.paypal.secret')
            )
        );

        $this->apiContext->setConfig([
            'mode' => config('services.paypal.mode'),
        ]);
    }

    public function processRefund(Request $request)
    {
        try {
            // Retrieve sale details
            $sale = Sale::get($request->payment_intent_id, $this->apiContext);
    
            // Check if the sale is refundable
            if ($sale->getState() !== 'completed') {
                return response()->json(['error' => 'Cannot refund a sale that is not completed.'], 400);
            }
    
            // Create refund request
            $refundRequest = new PaypalRefund();
            $refundRequest->setAmount(new Amount(['total' => $request->amount, 'currency' => $sale->getAmount()->getCurrency()]));
    
            // Perform refund
            $refund = $sale->refund($refundRequest, $this->apiContext);



            $change = Refund::where('id',$request->id)->first();
            $change->refund_status = $request->refund_status;
            $change->save();
    
    
            Payout::where('order_id',$change->order_id)->delete();
    
            $Order = Order::with('order_detail.products.product_single_gallery')->where('id',$change->order_id)->first();
            $user = User::where('id',$Order->customer_id)->first();
    
            $notification = new Notification();
            $notification->customer_id = $user->id;
            $notification->notification = 'your #'.$order->id.' refund request has been fulfield by admin and you will recive your amount in 5 to 10 working days.';
            $notification->save();
    
    
            Mail::send(
                'email.Order.order_refund',
                [
                    'buyer_name' => $user->name,
                    'order' => $Order,
                    // 'last_name' => $query->last_name
                ],
                function ($message) use ($user) {
                    $message->from('support@dragonautomart.com','Dragon Auto Mart');
                    $message->to($user->email);
                    $message->subject('Order Refund');
                }
            );

    
            // Check if the refund was successful
            if ($refund->getState() === 'completed') {
                return response()->json(['message' => 'Refund processed successfully']);
            } else {
                return response()->json(['error' => 'Refund failed.'], 500);
            }
        } catch (PayPalConnectionException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
