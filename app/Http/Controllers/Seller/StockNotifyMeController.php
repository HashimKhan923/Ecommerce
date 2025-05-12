<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockNotifyMe;
use App\Models\Product;
use Mail;
use App\Mail\ProductBackInStock;


class StockNotifyMeController extends Controller
{
    public function index($seller_id)
    {
      $data = StockNotifyMe::where('seller_id',$seller_id)->get();

      return response()->json(['data'=>$data]);
    }

    public function notify($id)
    {
        $data = StockNotifyMe::find($id);
    
        
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
                else
                {
                    return response(['status' => false, "message" => "The Product is out of stock!"], 400);
                }
    
            } else {
                // Case 2: Notify when the main product is back in stock
                $productAvailable = Product::where('id', $data->product_id)
                    ->whereHas('stock', function ($query) {
                    $query->where('stock', '>', 0);
                    })
                    ->exists();
    
                if ($productAvailable) {
                    // Send notification for the product back in stock
                    $this->notifyCustomer($data);
                    // Mark as notified
                    $data->status = 'notified';
                    $data->save();
                }
                else
                {
                    return response(['status' => false, "message" => "The Product is out of stock!"], 400);
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
            $product->product_single_gallery->image,
            $product->name,
            $variantName,
            'https://dragonautomart.com/product/'.$product->id
        ));
    }
}
