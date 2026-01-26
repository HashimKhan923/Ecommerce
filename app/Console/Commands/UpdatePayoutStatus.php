<?php

// namespace App\Console\Commands;

// use Illuminate\Console\Command;
// use App\Models\Payout;
// use Carbon\Carbon;
// use App\Models\User;
// use App\Models\BankDetail;
// use App\Models\Notification;
// use Stripe\Stripe;
// use Stripe\Transfer;
// use Mail;

// class UpdatePayoutStatus extends Command
// {
//     /**
//      * The name and signature of the console command.
//      *
//      * @var string
//      */
//     protected $signature = 'payout:update';
//     protected $description = 'Update payout status to paid after one week';

//     /**
//      * The console command description.
//      *
//      * @var string
//      */


//     public function handle()
//     {

//         $DaysAgo = Carbon::now()->subDays(5);

//         $payouts = Payout::where('created_at', '<=', $DaysAgo)
//                         ->where('status', '!=', 'Paid')
//                         ->get();


//                         foreach ($payouts as $payout) {
//                             $Seller = User::where('id',$payout->seller_id)->first();

//                             if($Seller->stripe_account_id != null)
//                                         {
//                                             Stripe::setApiKey(config('services.stripe.secret'));

                                
//                                             try {
//                                                 Transfer::create([
//                                                     'amount' => $payout->amount * 100,
//                                                     'currency' => 'usd',
//                                                     'destination' => $Seller->stripe_account_id,
//                                                 ]);

//                                             } catch (\Exception $e) {
//                                                 $this->error('Stripe Payout Error: ' . $e->getMessage());
//                                                 continue;
//                                             }

                                            
//                             }

//                             $payout->status = 'Paid';
//                             $payout->save();

//                             Notification::create([
//                                 'customer_id' => $Seller->id,
//                                 'notification' => 'your payout $'.$payout->amount.' has been successfully processed.'
//                             ]);

//                             Mail::send(
//                                 'email.Payout.seller_payout',
//                                 [
//                                     'vendor_name' => $Seller->name,
//                                     'amount' => $payout->amount,
//                                 ],
//                                 function ($message) use ($Seller) { 
//                                     $message->from('support@dragonautomart.com','Dragon Auto Mart');
//                                     $message->to($Seller->email);
//                                     $message->subject('Payout Notification');
//                                 }
//                             );
//                         }                


//         $this->info('Payouts paid successfully.');

        
//     }

// }




namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payout;
use App\Models\Order;
use Carbon\Carbon;
use App\Models\User;
use App\Models\BankDetail;
use App\Models\Notification;
use Stripe\Stripe;
use Stripe\Transfer;
use Mail;

class UpdatePayoutStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payout:update';
    protected $description = 'Update payout status to paid after one week';

    /**
     * The console command description.
     *
     * @var string
     */

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $orders = Order::where('delivery_status', 'Delivered')
            ->with(['payout' => function ($query) {
                $query->where('status', '!=', 'Paid');
            }])
            ->get();

        $DaysAgo = Carbon::now()->subDays(5);
        if($orders)
        {
            foreach($orders as $order)
            {
                $payouts = Payout::where('order_id',$order->id)
                ->where('created_at', '<=', $DaysAgo)
                ->where('status', '!=', 'Paid')
                ->get();


                foreach ($payouts as $payout) {
                    $Seller = User::where('id',$payout->seller_id)->first();

                    if($Seller->stripe_account_id != null)
                                {
                                    Stripe::setApiKey(config('services.stripe.secret'));

                        
                            try {
                                Transfer::create([
                                    'amount' => $payout->amount * 100,
                                    'currency' => 'usd',
                                    'destination' => $Seller->stripe_account_id,
                                ]);

                            } catch (\Stripe\Exception\ApiErrorException $e) {

                                // Detect insufficient funds
                                if (str_contains($e->getMessage(), 'Insufficient funds')) {

                                    // 🔔 Send email to admin
                                    Mail::send(
                                        'email.Payout.insufficient_funds',
                                        [
                                            'payout_id' => $payout->id,
                                            'seller_name' => $Seller->name,
                                            'seller_email' => $Seller->email,
                                            'amount' => $payout->amount,
                                            'error' => $e->getMessage(),
                                        ],
                                        function ($message) {
                                            $message->from('support@dragonautomart.com', 'Dragon Auto Mart');
                                            $message->to('support@dragonautomart.com'); // admin email
                                            $message->subject('Stripe Payout Failed – Insufficient Funds');
                                        }
                                    );

                                    // ❌ Do NOT mark payout as paid
                                    $this->error('Stripe Error (Insufficient Funds) for payout ID: ' . $payout->id);
                                    continue;
                                }

                                // Other Stripe errors
                                $this->error('Stripe Payout Error: ' . $e->getMessage());
                                continue;
                            }

                                    
                    }

                    $payout->status = 'Paid';
                    $payout->save();

                    Notification::create([
                        'customer_id' => $Seller->id,
                        'notification' => 'your payout $'.$payout->amount.' has been successfully processed.'
                    ]);

                    Mail::send(
                        'email.Payout.seller_payout',
                        [
                            'vendor_name' => $Seller->name,
                            'amount' => $payout->amount,
                        ],
                        function ($message) use ($Seller) { 
                            $message->from('support@dragonautomart.com','Dragon Auto Mart');
                            $message->to($Seller->email);
                            $message->subject('Payout Notification');
                        }
                    );
                } 
            }
        }
        else
        {
            $this->info('Payouts Not available.');
        }


               


        $this->info('Payouts paid successfully.');

        
    }

}
