<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Deal;
use Carbon\Carbon;

class UpdateDealStatus extends Command
{
    // The name and signature of the console command.
    protected $signature = 'deals:update-status';

    // The console command description.
    protected $description = 'Update product deal status when deal time is completed';

    // Execute the console command.
    public function handle()
    {
        // Get current time
        $now = Carbon::now();

        // Find the current active deal where the end time has expired
        $expiredDeal = Deal::where('discount_end_date', '<', $now)->first();

        // Check if the deal is expired
        if ($expiredDeal) {
            // Find all products associated with the expired deal
            $dealProducts = Product::where('deal_id', $expiredDeal->id)->get();

            // Update each product's deal status (set deal_id to null)
            foreach ($dealProducts as $dealProduct) {
                $dealProduct->update(['deal_id' => null]);
            }

            // Optional: You may want to mark the deal as expired or inactive in the deals table
            // $expiredDeal->update(['status' => 'expired']);

            $this->info('Deal status updated and associated products updated successfully!');
        } else {
            $this->info('No expired deals found.');
        }
    }
}
