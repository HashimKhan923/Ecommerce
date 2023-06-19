<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Banner;

class ProductController extends Controller
{
    public function index()
    {
        $Products = Product::with('user','category','brand','stock','discount','tax','shipping')->where('published',1)->where('approved',1)->get();
        $Categories = Category::where('is_active',1)->get();
        $Barnds = Brand::where('is_active',1)->get();
        $Banners = Banner::where('status',1)->get();
    }

    public function detail($id)
    {
        $Products = Product::with('user','category','brand','stock','discount','tax','shipping')->where('id',$id)->first();
    }
}
