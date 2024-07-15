<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payout;

class PayoutController extends Controller
{
    public function index($id)
    {
        $data = Payout::with('order.order_detail.products.shop','order.shop','order.nagative_payout_balance','listing_fee','featuredProductOrders','seller')->where('seller_id',$id)->get();

        return response()->json(['data'=>$data]);
    }

    public function create(Request $request)
    {
        $new = new Payout();
        $new->date = $request->date;
        $new->seller_id = $request->seller_id;
        $new->total_amount_topay = $request->total_amount_topay;
        $new->requested_amount = $request->requested_amount;
        $new->message = $request->message;
        $new->save();

        $response = ['status'=>true,"message" => "Payout Request Created Successfully!"];
        return response($response, 200);
    }

    public function delete($id)
    {
        Payout::find($id)->delete();
        
        $response = ['status'=>true,"message" => "Payout Request Deleted Successfully!"];
        return response($response, 200);
    }


    public function create_stripe_connect_account(Request $request)
    {
            $account = Account::create([
                        'type' => 'custom', 
                        'country' => $request->business_country, 
                        'email' => $request->email,
                        'business_type' => 'individual',
                        'capabilities' => [
                            'card_payments' => ['requested' => true],
                            'bank_transfer_payments' => ['requested' => true],
                            'transfers' => ['requested' => true],
                        ],
                        'tos_acceptance' => [
                            'date' => strtotime(now()),
                            'ip' => $request->ip(),
                        ],
                        'business_profile' => [
                            'name' => $request->shop_name,
                            // replace the request name with shop->id
                            'url' => 'https://dragonautomart.com/store/' . $shop->id,
                            'mcc' => '5533',
                        ],
                        'settings' => [
                            'payouts' => [
                                'statement_descriptor'=> $request->shop_name
                            ],
                            'payments' => [
                                'statement_descriptor'=> $request->shop_name
                            ]
                            
                        ],
                        'company' => [
                            'name' => 'Dragonautomart LLC',
                            'tax_id' => '933028427', 
                        ],
                        'individual' => [
                            'id_number' => $request->ssn,
                        //'id_number' => '933-02-8427',
                            'first_name' => $request->business_first_name,
                            'last_name' => $request->business_last_name,
                            'email' => $request->business_email,
                            'phone' => $request->business_phone_number,
                            'dob' => [
                                'day' => $request->business_date,
                                'month' => $request->business_month,
                                'year' => $request->business_year,
                            ],
                            'ssn_last_4' => $request->last_4_ssn,
                            'address' => [
                                'line1' => $request->address1,
                            'line2' => $request->address2,
                                'city' => $request->business_city,
                                'state' => $request->business_state,
                                'postal_code' => $request->business_zip_code,
                                'country' => $request->business_country,

                            ],
                        ],
                    ]);

                    $bankAccount = $account->external_accounts->create([
                        'external_account' => [
                            'object' => 'bank_account',
                            'country' => $request->business_country,
                            'currency' => 'usd',
                            'account_holder_name' => $request->account_title,
                            'account_holder_type' => 'individual',
                            'routing_number' => $request->routing_number, 
                            'account_number' => $request->account_number,
                        ],
                    ]);
    }
}
