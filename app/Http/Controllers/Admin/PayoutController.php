<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payout;
use Stripe\Stripe;
use Stripe\Transfer;

class PayoutController extends Controller
{
    public function index()
    {
        $data = Payout::all();

        return response()->json(['data'=>$data]);
    }

    public function payment(Request $request)
    {

        
        $vendor = User::findOrFail($vendorId);

        $bankAccountDetails = [
            'account_number' => 'XXXXXXXXXXXX', 
            'routing_number' => 'YYYYYYYYY',    
            'account_holder_name' => 'Vendor Name',            
        ];

        
        Stripe::setApiKey(config('services.stripe.secret'));

        
        $transfer = Transfer::create([
            'amount' => $request->amount * 100,
            'currency' => 'usd',
            'source_transaction' => $request->source_transaction,
            'destination' => $bankAccountDetails,
        ]);

        

        return response()->json(['message' => 'Payment made successfully']);
    
    }
}
