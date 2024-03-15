<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payout;
use App\Models\User;
use App\Models\BankDetail;
use Stripe\Stripe;
use Stripe\Transfer;

class PayoutController extends Controller
{
    public function index()
    {
        $data = Payout::with('order.order_detail.products.shop')->get();

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
                'amount' => $request->amount * 100,
                'currency' => 'usd',
                'destination' => $Seller->stripe_account_id,
            ]);
        // } catch (\Stripe\Exception\CardException $e) {
        //     // Handle specific card errors
        //     dd($e->getError());
        // } catch (\Stripe\Exception\ApiErrorException $e) {
        //     // Handle general API errors
        //     dd($e->getError());
        } catch (\Exception $e) {
            return response()->json(['status' => false,'message'=>$e->getMessage(), 422]);
        }



        if($PaymentStatus->status == 'Un Paid')
        {
            $PaymentStatus->status = 'Paid';
        }
        else
        {
            $PaymentStatus->status = 'Un Paid';
        }
        
        $PaymentStatus->save();


        return response()->json(['message' => 'Paid Successfully']);
    
    }
}
