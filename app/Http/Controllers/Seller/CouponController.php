<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\CouponCategory;
use App\Models\CouponCustomer;
use App\Models\CouponProduct;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\MyCustomer;
use App\Models\User;
use Carbon\Carbon;
use Mail;

class CouponController extends Controller
{
    public function index($seller_id)
    {
        $data = Coupon::with('creator','shop','coupon_customers.customer','coupon_categories.category','coupon_products.product.product_gallery')->where('creator_id',$seller_id)->get();

        return response()->json(['data'=>$data]);
    }

    public function create(Request $request)
    {
        $new = new Coupon();
        $new->creator_id = $request->creator_id;
        $new->shop_id = $request->shop_id;
        $new->name = $request->name;
        $new->code = $request->code;
        $new->discount = $request->discount;
        $new->discount_type = $request->discount_type;
        $new->minimum_purchase_amount = $request->minimum_purchase_amount;
        $new->minimum_quantity_items = $request->minimum_quantity_items;
        $new->is_amount_order = $request->is_amount_order;
        $new->is_free_shipping = $request->is_free_shipping;
        $new->start_date = Carbon::parse($request->start_date);
        $new->end_date = Carbon::parse($request->end_date);
        $new->save();
        
        $shop = Shop::find($request->shop_id);

        $emailView = '';
        $emailData = [
            'coupon' => $new,
            'shop' => $shop,
        ];
    
        if ($request->category_id) {
            $emailView = 'email.Coupon.category';
            $categories = Category::whereIn('id', $request->category_id)->get();
            $emailData['categories'] = $categories;
            foreach ($request->category_id as $category_id) {
                $CouponCategory = new CouponCategory();
                $CouponCategory->coupon_id = $new->id;
                $CouponCategory->category_id = $category_id;
                $CouponCategory->save();
            }
        }
    
        if ($request->product_id) {
            $emailView = 'email.Coupon.product';
            $products = Product::with('product_single_gallery')->whereIn('id', $request->product_id)->get();
            $emailData['products'] = $products;
            foreach ($request->product_id as $product_id) {
                $CouponProduct = new CouponProduct();
                $CouponProduct->coupon_id = $new->id;
                $CouponProduct->product_id = $product_id;
                $CouponProduct->save();
            }
        }
    
        if ($new->is_amount_order) {
            $emailView = 'email.Coupon.amount_order';
        }
    
        if ($new->is_free_shipping) {
            $emailView = 'email.Coupon.free_shipping';
        }
    
        if ($request->customer_id) {
            foreach ($request->customer_id as $customer_id) {
                $CouponCustomer = new CouponCustomer();
                $CouponCustomer->coupon_id = $new->id;
                $CouponCustomer->customer_id = $customer_id;
                $CouponCustomer->save();
            }
        }
    
        if ($request->customer_id) {
            foreach ($request->customer_id as $customer_id) {
                $customer = User::find($customer_id);
                Mail::send($emailView, $emailData, function ($message) use ($customer) {
                    $message->from('support@dragonautomart.com', 'Dragon Auto Mart');
                    $message->to($customer->email);
                    $message->subject('New Coupon Available for You!');
                });
            }
        }
        else
        {
            $MyCustomers = MyCustomer::where('seller_id',$request->creator_id)->get();
            foreach ($MyCustomers as $customer) {
                $customer = MyCustomer::find($customer->customer_id)->first();
                Mail::send($emailView, $emailData, function ($message) use ($customer) {
                    $message->from('support@dragonautomart.com', 'Dragon Auto Mart');
                    $message->to($customer->email);
                    $message->subject('New Coupon Available for You!');
                });
            }

        }
    
        $response = ['status' => true, 'message' => 'Coupon Created Successfully!', 'coupon_id' => $new->id];
        return response($response, 200);
    }
    
    

    public function update(Request $request)
    {
        $update = Coupon::where('id',$request->id)->first();
        $update->shop_id = $request->shop_id;
        $update->name = $request->name;
        $update->code = $request->code;
        $update->discount = $request->discount;
        $update->discount_type = $request->discount_type;
        $update->minimum_purchase_amount = $request->minimum_purchase_amount;
        $update->minimum_quantity_items = $request->minimum_quantity_items;
        $update->minimum_quantity_items = $request->minimum_quantity_items;
        $update->is_amount_order = $request->is_amount_order;
        $update->is_free_shipping = $request->is_free_shipping;
        $update->start_date = Carbon::parse($request->start_date);
        $update->end_date = Carbon::parse($request->end_date);
        $update->save();

        if($request->customer_id)
        {
            CouponCustomer::where('coupon_Id',$update->id)->delete();

            foreach($request->customer_id as $customer_id)
            {
                $CouponCustomer = new CouponCustomer();
                $CouponCustomer->coupon_id = $update->id;
                $CouponCustomer->customer_id = $customer_id;
                $CouponCustomer->save();
            }

        }
        else
        {
            CouponCustomer::where('coupon_Id',$update->id)->delete();
        }

        if($request->category_id)
        {
            CouponCategory::where('coupon_Id',$update->id)->delete();
            CouponProduct::where('coupon_Id',$update->id)->delete();

            foreach($request->category_id as $category_id)
            {
                $CouponCategory = new CouponCategory();
                $CouponCategory->coupon_id = $update->id;
                $CouponCategory->category_id = $category_id;
                $CouponCategory->save();
            }

        }

        if($request->product_id)
        {
            CouponProduct::where('coupon_Id',$update->id)->delete();
            CouponCategory::where('coupon_Id',$update->id)->delete();

            foreach($request->product_id as $product_id)
            {
                $CouponProduct = new CouponProduct();
                $CouponProduct->coupon_id = $update->id;
                $CouponProduct->product_id = $product_id;
                $CouponProduct->save();
            }

        }


        $response = ['status'=>true,"message" => "Coupon Updated Successfully!"];
        return response($response, 200);
    }

    public function delete($id)
    {
        Coupon::find($id)->delete();

        $response = ['status'=>true,"message" => "Coupon Deleted Successfully!"];
        return response($response, 200);
    }

    public function status($id)
    {
        $status = Coupon::find($id);

        if($status->status == 1)
        {
            $status->status = 0;
        }
        else
        {
            $status->status = 1;
        }
        $status->save();

        $response = ['status'=>true,"message" => "Status Changed Successfully!"];
        return response($response, 200);
    }

    public function multi_delete(Request $request)
    {
        Coupon::whereIn('id',$request->ids)->delete();


        $response = ['status'=>true,"message" => "Coupons Deleted Successfully!"];
        return response($response, 200);
    }
}
