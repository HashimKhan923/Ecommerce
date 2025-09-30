<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payout;
use App\Models\User;
use App\Models\BankDetail;
use App\Models\Notification;
use Stripe\Stripe;
use Stripe\Transfer;
use Mail;

class PayoutController extends Controller
{
    public function index($shop_id = null, $start = 0, $length = 10, $status = null, $searchValue = null)
    {
        $query = Payout::with('order.shop','listing_fee','featuredProductOrders','seller');


        if ($shop_id) {
            $query->where('shop_id', $shop_id);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($searchValue) {
            $query->where('order_id',$searchValue);
        }

        $data = $query->orderBy('id', 'desc')
        ->skip($start)
        ->take($length)
        ->get();

        return response()->json(['data'=>$data]);
    }



    public function status($payout_id)
    {

        $PaymentStatus = Payout::where('id',$payout_id)->first();
        $Seller = User::where('id',$PaymentStatus->seller_id)->first();

        if($Seller->stripe_account_id == null)
        {
            return response()->json(['status'=>false,'message' => 'This Seller Account is Not Found in Stripe Connect',404]);

        }
        
        Stripe::setApiKey(config('services.stripe.secret'));

        
        try {
            Transfer::create([
                'amount' => $PaymentStatus->amount * 100,
                'currency' => 'usd',
                'destination' => $Seller->stripe_account_id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false,'message'=>$e->getMessage(), 422]);
        }

            $PaymentStatus->status = 'Paid';
            $PaymentStatus->save();

            Notification::create([
                'customer_id' => $Seller->id,
                'notification' => 'your payout $'.$PaymentStatus->amount.' has been successfully processed.'
            ]);

            Mail::send(
                'email.Payout.seller_payout',
                [
                    'vendor_name' => $Seller->name,
                    'amount' => $PaymentStatus->amount,
                ],
                function ($message) use ($Seller, $request) { 
                    $message->from('support@dragonautomart.com','Dragon Auto Mart');
                    $message->to($Seller->email);
                    $message->subject('Payout Notification');
                }
            );


        return response()->json(['message' => 'Paid Successfully']);
    
    }
}
