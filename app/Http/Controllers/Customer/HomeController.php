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
use App\Models\Shop;
use DB;

class HomeController extends Controller
{
    public function index()
    {
        $Products = Product::with('user','category','brand','model','stock','product_gallery','discount','tax','shipping','deal.deal_product','wholesale','shop','reviews.user','product_varient')->where('published',1)->get();

        $TopSelling = Product::with('user', 'category', 'brand', 'model', 'stock', 'product_gallery', 'discount', 'tax', 'shipping', 'deal.deal_product', 'wholesale', 'shop', 'reviews.user', 'product_varient')
        ->where('published', 1)
        ->orderBy('num_of_sale', 'desc')
        ->get();

        $TrendingProducts = Product::with('user', 'category', 'brand', 'model', 'stock', 'product_gallery', 'discount', 'tax', 'shipping', 'deal.deal_product', 'wholesale', 'shop', 'reviews.user', 'product_varient')
        ->where('published', 1)
        ->with(['reviews' => function ($query) {
            $query->select('product_id', DB::raw('AVG(rating) as avg_rating'))
                ->groupBy('product_id');
        }])
        ->orderByDesc('reviews.avg_rating')
        ->get();

        $Categories = Category::where('is_active',1)->get();
        $Brands = Brand::with('model')->where('is_active',1)->get();
        $Banners = Banner::where('status',1)->get();
        $HomeBanners = HomeBanner::first();
        $States = State::all();
        $Shops = Shop::with('seller','product.product_gallery','product.category','product.brand','product.model','product.stock','product.product_varient','product.reviews.user','product.tax')->get();

        return response()->json(['Products'=>$Products,'TopSelling'=>$TopSelling,'TrendingProducts'=>$TrendingProducts,'Categories'=>$Categories,'Brands'=>$Brands,'Banners'=>$Banners,'HomeBanners'=>$HomeBanners,'States'=>$States,'Shops'=>$Shops]);
    }

    
}
