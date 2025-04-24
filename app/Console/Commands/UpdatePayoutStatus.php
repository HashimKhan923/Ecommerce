<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payout;
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

        $DaysAgo = Carbon::now()->subDays(5);

        $payouts = Payout::where('created_at', '<=', $DaysAgo)
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

                                            } catch (\Exception $e) {
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


        $this->info('Payouts paid successfully.');

        
    }

}
