<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use App\Models\Payout as payout;
use App\Models\BankDetail;
use Stripe\Stripe;
use Stripe\Payout;

class PayoutController extends Controller
{
    public function index()
    {
        $data = Payout::all();

        return response()->json(['data'=>$data]);
    }

    public function payment(Request $request)
    {

        
        $BankDetail = BankDetail::where('seller_id',$request->seller_id)->first();

        $bankAccountDetails = [
            'account_number' => $BankDetail->account_number,
            'routing_number' => $BankDetail->routing_number,    
            'account_holder_name' => $BankDetail->account_title,           
        ];

        
        Stripe::setApiKey(config('services.stripe.secret'));

        
        Payout::create([
            'amount' => $request->amount * 100, // Amount in cents
            'currency' => 'usd',
            'destination' => $bankAccountDetails['account_number'], // Use the account number as the destination
            'source_type' => 'bank_account', // Specify the source type as a bank account
        ]);


        // $PaymentStatus = Payout::where('id',$request->payout_id)->first();
        // $PaymentStatus->payment_status = 'Paid';
        // $PaymentStatus->save();


        return response()->json(['message' => 'Payment made successfully']);
    
    }
}
