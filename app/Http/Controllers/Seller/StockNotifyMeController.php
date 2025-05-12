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
      $data = StockNotifyMe::with('product','varient.product')->where('seller_id',$seller_id)->get();

      return response()->json(['data'=>$data]);
    }

    public function notify($id)
    {
        $data = StockNotifyMe::find($id);
        if (!$data) {
            return response(['status' => false, 'message' => 'Notification data not found.'], 404);
        }
    
        if ($data->variant_id) {
            // Case 1: Variant stock check
            $variantAvailable = Product::where('id', $data->product_id)
                ->whereHas('product_varient', function ($query) use ($data) {
                    $query->where('id', $data->variant_id)->where('stock', '>', 0);
                })->exists();
    
            if ($variantAvailable) {
                $this->notifyCustomer($data);
                $data->status = 'notified';
                $data->save();
            } else {
                return response(['status' => false, 'message' => 'The Product is out of stock!'], 400);
            }
    
        } else {
            // Case 2: Main product stock check
            $productAvailable = Product::where('id', $data->product_id)
                ->whereHas('stock', function ($query) {
                    $query->where('stock', '>', 0);
                })->exists();
    
            if ($productAvailable) {
                $this->notifyCustomer($data);
                $data->status = 'notified';
                $data->save();
            } else {
                return response(['status' => false, 'message' => 'The Product is out of stock!'], 400);
            }
        }
    }
    
    protected function notifyCustomer($data)
    {
        $product = Product::find($data->product_id);
        if (!$product) return;
    
        $variantName = null;
        if ($data->variant_id) {
            $variant = $product->product_varient()->where('id', $data->variant_id)->first();
            $variantName = $variant ? $variant->name : null;
        }
    
        $image = $product->product_single_gallery->image ?? null;
    
        Mail::to($data->email)->send(new ProductBackInStock(
            $data->email,
            $image,
            $product->name,
            $variantName,
            'https://dragonautomart.com/product/' . $product->id
        ));
    }
    
}
