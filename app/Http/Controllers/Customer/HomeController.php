<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\SubscribeUser;
use App\Models\Banner;
use App\Models\HomeBanner;
use App\Models\State;
use App\Models\Shop;
use DB;

class HomeController extends Controller
{
    public function index()
    {

        $package = SubscribeUser::where('end_time', '<=', now())->orWhere('product_upload_limit', '<', 1)->first();

        if ($package) {
            Product::where('user_id', $package->user_id)->where('featured', 1)->update(['featured' => 0]);
        }
        
        if($package)
        {
            $package->delete();
        }

        $Products = Product::with('user','category','brand','model','stock','product_gallery','discount','tax','shipping','deal.deal_product','wholesale','shop','reviews.user','product_varient')->where('published',1)->get();

        $TopSelling = Product::with('user', 'category', 'brand', 'model', 'stock', 'product_gallery', 'discount', 'tax', 'shipping', 'deal.deal_product', 'wholesale', 'shop', 'reviews.user', 'product_varient')
        ->where('published', 1)
        ->orderBy('num_of_sale', 'desc')
        ->get();

        $TrendingProducts = Product::with('user', 'category', 'brand', 'model', 'stock', 'product_gallery', 'discount', 'tax', 'shipping', 'deal.deal_product', 'wholesale', 'shop', 'reviews.user', 'product_varient')
        ->where('published', 1)
        ->leftJoin('reviews', 'products.id', '=', 'reviews.product_id')
        ->select('products.*', DB::raw('AVG(reviews.rating) as avg_rating'))
        ->groupBy('products.id')
        ->orderByDesc('avg_rating')
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
