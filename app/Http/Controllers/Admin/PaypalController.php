<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Refund;
use PayPal\Api\Sale;
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

        // Create refund object
        $refund = new Refund();
        $refund->setAmount([
            'total' => $request->amount,
            'currency' => $sale->getAmount()->getCurrency(),
        ]);

        // Perform refund
        $refundResponse = $sale->refundTransaction($refund, $this->apiContext);

        // Check if the refund was successful
        if ($refundResponse->getState() === 'completed') {
            return response()->json(['message' => 'Refund processed successfully']);
        } else {
            return response()->json(['error' => 'Refund failed.'], 500);
        }
    } catch (PayPalConnectionException $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }

    }
}
