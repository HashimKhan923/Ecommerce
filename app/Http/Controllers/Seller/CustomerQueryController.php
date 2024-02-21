<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SellerContact;
use Mail;
use App\Models\ProductGallery;
use App\Models\Product;
use App\Models\User;
use App\Models\Shop;

class CustomerQueryController extends Controller
{
    public function index($seller_id)
    {
        $data = SellerContact::with('product.product_gallery','shop','seller','customer')->where('seller_id',$seller_id)->get();

        return response()->json(['data'=>$data]);
    }

    public function reply(Request $request)
    {
        $ProductName = Product::where('id',$request->product_id)->first();
        $Shop = Shop::where('id',$request->shop_id)->first();
        $ProductImage = ProductGallery::where('product_id',$request->product_id)->first();
        $Seller = User::where('id',$request->seller_id)->first();
        $Customer = User::where('id',$request->customer_id)->first();
        if($ProductName)
        {
            Mail::send(
                'email.customer_to_seller_query_with_product',
                [
                    'ProductName'=>$ProductName->name,
                    'ProductImage'=>$ProductImage->image,
                    'ShopName'=>$Shop->name,
                    'Customer'=>$Customer,
                    'Seller'=>$Seller,
                    'Msg'=>$request->message,
                    
                ], 

                function ($message) use ($Customer) {
                    $message->from('support@dragonautomart.com','Dragon Auto Mart');
                    $message->to($Customer->email);
                    $message->subject('Message Reply');
                });
        }
        else
        {
            Mail::send(
                'email.customer_to_seller_query_with_shop',
                [
                    'ShopName'=>$Shop->name,
                    'ShopImage'=>$Shop->logo,
                    'Customer'=>$Customer,
                    'Seller'=>$Seller,
                    'Msg'=>$request->message,
                    
                ], 

                function ($message) use ($Seller) {
                    $message->from('support@dragonautomart.com','Dragon Auto Mart');
                    $message->to($Customer->email);
                    $message->subject('Message Reply');
                });
        }

        $change = SellerContact::find($request->query_id);
        $change->reply_status = 'replied';
        $change->save();

        return response()->json(['message'=>'sent successfully!',200]);

    }
}
