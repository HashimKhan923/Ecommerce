<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\StockNotifyMe;

class NotifyCustomersProductBackInStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:back-in-stock';
    protected $description = 'Notify customers when products are back in stock';

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

        $datas = StockNotifyMe::where('status','pending')->get();

        foreach($datas as $data)
        {
            Product::where('id',$data->product_id)
                ->whereHas('stock', function ($query) {
                $query->where('stock', '>', 0);
            })
            ->orWhereHas('product_varient', function ($query,$data) {
                $query->where('id',$data->variant_id)
                ->where('stock', '>', 0);
            });
        }

        
    }
}
