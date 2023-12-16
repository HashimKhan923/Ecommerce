<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payout;
use App\Models\BankDetail;
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

        
        $BankDetail = BankDetail::where('seller_id',$request->seller_id)->first();

        $bankAccountDetails = [
            'account_number' => $BankDetail->account_number,
            'routing_number' => $BankDetail->routing_number,    
            'account_holder_name' => $BankDetail->account_title,           
        ];

        
        Stripe::setApiKey(config('services.stripe.secret'));

        
        try {
            Transfer::create([
                'amount' => $request->amount * 100,
                'currency' => 'usd',
                'destination' => $bankAccountDetails,
            ]);
        } catch (\Stripe\Exception\CardException $e) {
            // Handle specific card errors
            dd($e->getError());
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle general API errors
            dd($e->getError());
        } catch (Exception $e) {
            // Handle other exceptions
            dd($e->getMessage());
        }


        $PaymentStatus = Payout::where('id',$request->payout_id)->first();
        $PaymentStatus->payment_status = 'Paid';
        $PaymentStatus->save();





        

        return response()->json(['message' => 'Payment made successfully']);
    
    }
}
