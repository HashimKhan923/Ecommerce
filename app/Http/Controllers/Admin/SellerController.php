<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductGallery;
use App\Models\ProductVarient;
use Mail;
use Stripe\Stripe;
use Stripe\Account;
class SellerController extends Controller
{
    public function index()
    {
        // $Sellers = User::with('my_customers.customer','time_line','seller_order.order_timeline','seller_order.order_refund','stafs','shop.shop_policy','shop.product','seller_information','SellingPlatforms','SocialPlatforms','BankDetail','CreditCard')->where('user_type','seller')->get();
        $Sellers = User::with(['my_customers.customer', 'seller_time_line', 'seller_order.order_timeline', 'seller_order.order_refund', 'stafs', 'shop' => function($query) {
            $query->withCount('product');
        }, 'seller_information', 'SellingPlatforms', 'SocialPlatforms', 'BankDetail', 'CreditCard'])
        ->where('user_type', 'seller')
        ->get();
        return response()->json(["Sellers"=>$Sellers]);
    }

    public function is_active($id)
    {
        $is_active = User::where('id',$id)->first();

        if($is_active->is_active == 0)
        {
            $is_active->is_active = 1;
        }
        else
        {
            $is_active->is_active = 0;
        }

        $is_active->save();

        $response = ['status'=>true,"message" => "Status Changed Successfully!"];
        return response($response, 200);
    }

    public function is_verify($id)
    {
        $is_active = User::where('id',$id)->first();
        $is_active->is_verify = 1;
        $is_active->save();

        Mail::send(
            'email.seller_account_verification',
            [
                'name'=>$is_active->name,
            ], 
        
        function ($message) use ($is_active) {
            $message->from('support@dragonautomart.com','Dragon Auto Mart');
            $message->to($is_active->email);
            $message->subject('Account Verification');
        });

        $response = ['status'=>true,"message" => "Status Changed Successfully!"];
        return response($response, 200);
    }


    public function delete($id)
    {
        $seller = User::find($id);
    
        if (!$seller) {
            return response(['status' => false, 'message' => 'Seller not found!'], 404);
        }
    
        // Check for products under this seller
        $products = Product::where('user_id', $id)->get();
    
        if ($products->isNotEmpty()) {
            foreach ($products as $product) {
                $this->deleteProductRelatedImages($product->id, 'ProductGallery', 'ProductGallery');
                $this->deleteProductRelatedImages($product->id, 'ProductVarient', 'ProductVarient');
            }
        }
    
        // Delete the Stripe account if it exists
        if ($seller->stripe_account_id) {
            Stripe::setApiKey(config('services.stripe.secret'));
    
            try {
                $account = Account::retrieve($seller->stripe_account_id);
                $account->delete();
            } catch (\Exception $e) {
                return response(['status' => false, 'message' => 'Error deleting Stripe account: ' . $e->getMessage()], 500);
            }
        }
    
        $seller->delete();
    
        return response(['status' => true, 'message' => 'Seller and Stripe account deleted successfully!'], 200);
    }

    public function multi_delete(Request $request)
    {
        $sellerIds = $request->ids;
        foreach ($sellerIds as $id) {
            $seller = User::find($id);

            if (!$seller) {
                continue; // Skip this user if not found
            }

            // Check for products under this seller
            $products = Product::where('user_id', $id)->get();

            if ($products->isNotEmpty()) {
                foreach ($products as $product) {
                    $this->deleteProductRelatedImages($product->id, 'ProductGallery', 'ProductGallery');
                    $this->deleteProductRelatedImages($product->id, 'ProductVarient', 'ProductVarient');
                }
            }

            // Delete the Stripe account if it exists
            if ($seller->stripe_account_id) {
                Stripe::setApiKey(config('services.stripe.secret'));

                try {
                    $account = Account::retrieve($seller->stripe_account_id);
                    $account->delete();
                } catch (\Exception $e) {
                    // Log the error but continue deleting other users
                    \Log::error("Error deleting Stripe account for user ID {$id}: " . $e->getMessage());
                    continue;
                }
            }

            $seller->delete();
        }

        return response(['status' => true, 'message' => 'Users and their Stripe accounts deleted successfully!'], 200);
    }
    
    private function deleteProductRelatedImages($productId, $relatedModel, $folder)
    {
        $relatedItems = app("App\\Models\\{$relatedModel}")->where('product_id', $productId)->get();
    
        foreach ($relatedItems as $item) {
            $fileToDelete = public_path("{$folder}/{$item->image}");
    
            if (file_exists($fileToDelete)) {
                unlink($fileToDelete);
            }
        }
    }
    

    public function strip_account_delete($stripe_id)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        try {
            $account = Account::retrieve($stripe_id);
            $account->delete();

            $response = ['status' => true, 'message' => 'Stripe account deleted successfully!'];
            return response($response, 200);
        } catch (\Exception $e) {
            $response = ['status' => false, 'message' => 'Error deleting Stripe account: ' . $e->getMessage()];
            return response($response, 500);            
        }


    }
}
