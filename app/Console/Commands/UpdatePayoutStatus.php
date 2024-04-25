<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payout;
use Carbon\Carbon;
use App\Models\User;
use App\Models\BankDetail;
use Stripe\Stripe;
use Stripe\Transfer;

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
        $payouts = Payout::where('status', 'Un Paid')->get();

        foreach ($payouts as $payout) {
            $Seller = User::where('id',$payout->seller_id)->first();
            $startDate = Carbon::parse($payout->created_at);
            // $endDate = $startDate->copy()->addWeekdays(1);

            if (Carbon::parse($payout->created_at)->diffInDays(now()) >= 1 && !$this->isWeekend($startDate)) {

                // if($Seller->stripe_account_id != null)
                // {
                //     Stripe::setApiKey(config('services.stripe.secret'));

        
                //     try {
                //         Transfer::create([
                //             'amount' => $payout->amount * 100,
                //             'currency' => 'usd',
                //             'destination' => $Seller->stripe_account_id,
                //         ]);

                //     } catch (\Exception $e) {
                //         return response()->json(['status' => false,'message'=>$e->getMessage(), 422]);
                //     }

                //     $payout->update(['status' => 'Paid']);
                // }


                
            }
        }

        $this->info('Payouts paid successfully.');

        
    }


   protected function isWeekend(Carbon $date)
   {

        return $date->isWeekend();

   }
}
