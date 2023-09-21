<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Banner;
use Mail;

class HomeController extends Controller
{
    public function index()
    {

        Mail::send(
            'email.order_information',
            [
                'buyer_name' => 'khan',
                // 'last_name' => $query->last_name
            ],
            function ($message) { // Add $user variable here
                $message->from('support@dragonautomart.com');
                $message->to('khanhash1994@gmail.com');
                $message->subject('Order Confirmation');
            }
        );

        $Products = Product::with('user','category','brand','model','stock','varient','discount','tax','shipping','deal.deal_product','wholesale','shop')->where('published',1)->get();
        $Categories = Category::where('is_active',1)->get();
        $Brands = Brand::with('model')->where('is_active',1)->get();
        $Banners = Banner::where('status',1)->get();

        return response()->json(['Products'=>$Products,'Categories'=>$Categories,'Brands'=>$Brands,'Banners'=>$Banners]);
    }

    
}
