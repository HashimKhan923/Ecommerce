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
            $seller = User::where('id', $payout->seller_id)->first();
            $startDate = Carbon::parse($payout->created_at);
    
            // if (Carbon::parse($payout->created_at)->diffInDays(now()) >= 5 && !$this->isWeekend($startDate)) {
    
            //     if ($seller->stripe_account_id != null) {
            //         Stripe::setApiKey(config('services.stripe.secret'));
    
            //         try {
            //             Transfer::create([
            //                 'amount' => $payout->amount * 100,
            //                 'currency' => 'usd',
            //                 'destination' => $seller->stripe_account_id,
            //             ]);
    
            //             $payout->update(['status' => 'Paid']);
    
            //         } catch (\Exception $e) {
            //             $this->error($e->getMessage());
            //         }
            //     }
            // }
        }
    
        $this->info('Payouts paid successfully.');
    }


   protected function isWeekend(Carbon $date)
   {

        return $date->isWeekend();

   }
}
