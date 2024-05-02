<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Coupon;

class CouponUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coupon:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'coupon update successfully!';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $coupons = Coupon::where('end_date', '<=', now())
        ->get();

        if($coupons->isNotEmpty())
        {
            Coupon::where('end_date', '<=', now())->update(['status' => 0]);

        }

        $this->info('Payouts paid successfully.');
    }
}
