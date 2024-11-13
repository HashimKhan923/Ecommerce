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
        $datas = StockNotifyMe::where('status', 'pending')->get();
    
        foreach ($datas as $data) {
            if ($data->variant_id) {
                // Case 1: Notify when the variant is back in stock
                $variantAvailable = Product::where('id', $data->product_id)
                    ->whereHas('product_varient', function ($query) use ($data) {
                        $query->where('id', $data->variant_id)
                              ->where('stock', '>', 0);
                    })->exists();
    
                if ($variantAvailable) {
                    // Send notification for the variant back in stock
                    $this->notifyCustomer($data);
                    // Mark as notified
                    $data->status = 'notified';
                    $data->save();
                }
    
            } else {
                // Case 2: Notify when the main product is back in stock
                $productAvailable = Product::where('id', $data->product_id)
                    ->where('stock', '>', 0)
                    ->exists();
    
                if ($productAvailable) {
                    // Send notification for the product back in stock
                    $this->notifyCustomer($data);
                    // Mark as notified
                    $data->status = 'notified';
                    $data->save();
                }
            }
        }
    }



    protected function notifyCustomer($data)
    {
        // Example of sending an email notification to the customer
        $product = Product::find($data->product_id);
        $variantName = $data->variant_id ? $product->product_varient->find($data->variant_id)->name : null;
        
        // Use the ProductBackInStock Mailable to send an email
        Mail::to($data->email)->send(new ProductBackInStock(
            $data->email,
            $product->name,
            $variantName,
            'https://dragonautomart.com/product/'.$product->id
        ));
    }
    
}
