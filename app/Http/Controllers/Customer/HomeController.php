<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Banner;

class HomeController extends Controller
{
    public function index()
    {
        $Products = Product::where('published',1)->get();
        $Categories = Category::where('is_active',1)->get();
        $Brands = Brand::where('is_active',1)->get();
        $Banners = Banner::where('status',1)->get();

        return response()->json(['Products'=>$Products,'Categories'=>$Categories,'Brands'=>$Brands,'Banners'=>$Banners]);
    }

    
}
