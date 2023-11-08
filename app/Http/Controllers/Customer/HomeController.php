<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Banner;
use App\Models\HomeBanner;
use App\Models\State;

class HomeController extends Controller
{
    public function index()
    {
        $Products = Product::with('user','category','brand','model','stock','product_gallery','discount','tax','shipping','deal.deal_product','wholesale','shop','reviews.user','product_varient')->where('published',1)->get();
        $Categories = Category::where('is_active',1)->get();
        $Brands = Brand::with('model')->where('is_active',1)->get();
        $Banners = Banner::where('status',1)->get();
        $HomeBanners = HomeBanner::first();
        $States = State::all();



        return response()->json(['Products'=>$Products,'Categories'=>$Categories,'Brands'=>$Brands,'Banners'=>$Banners,'HomeBanners'=>$HomeBanners,'States'=>$States]);

    }

    
}
