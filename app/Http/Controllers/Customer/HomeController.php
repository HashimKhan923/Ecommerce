<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use App\Models\SubscribeUser;
use App\Models\Banner;
use App\Models\AllBanner;
use App\Models\State;
use App\Models\Shop;
use App\Models\Models;
use App\Models\Deal;
use DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        return Carbon::now()->format('Y-m-d H:i:s');
        $FeaturedProducts = Product::with([
            'user', 'category', 'sub_category', 'brand', 'model', 'stock',
            'product_gallery' => function ($query) {
                $query->orderBy('order', 'asc');
            },
            'discount', 'tax', 'shipping', 'deal', 'shop.shop_policy', 'reviews.user', 'product_varient'
        ])->where('published', 1)
          ->whereHas('stock', function ($query) {
              $query->where('stock', '>', 0);
          })->whereHas('shop', function ($query) {
              $query->where('status', 1);
          })->where('featured', 1)
          ->orderByRaw('RAND()') 
          ->take(50)
          ->get();
    
        $DealProducts = Product::with([
            'user', 'category','sub_category','brand', 'model', 'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            },
            'discount','deal','tax', 'shipping',
            'shop.shop_policy', 'reviews.user', 'product_varient'
        ])->where('published', 1)->whereHas('stock', function ($query) {
            $query->where('stock', '>', 0);
        })->whereHas('shop', function ($query) {
            $query->where('status', 1);
        })->where('deal_id',4)
        ->orderByRaw('RAND()') 
        ->take(50)
        ->get();

    

        $Categories = Category::with(['sub_category' => function ($query) {
            $query->orderBy('order', 'asc');
        }])
        ->where('is_active', 1)
        ->orderBy('order', 'asc')
        ->get();
    

        $Brands = Brand::with('model')->withCount('product')->where('is_active', 1)->orderByDesc('product_count')->get();
        $Banners = Banner::where('status', 1)->get();
        $States = State::where('status', 1)->get();
        $SubCategories = SubCategory::with('category')->where('is_active', 1)->get();
        $Models = Models::where('is_active',1)->get();
        $AllBanners = AllBanner::where('status', 1)->get();
        $Shops = Shop::with('seller', 'shop_policy')->where('status',1)->where('featured', 1)->get();
        $Deal = Deal::where('discount_start_date', '<=', now()->timezone(config('app.timezone'))->format('Y-m-d H:i:s'))
        ->where('discount_end_date', '>=', now()->timezone(config('app.timezone'))->format('Y-m-d H:i:s'))
        ->where('status', 1)
        ->get();


    
        return response()->json([
            'FeaturedProducts' => $FeaturedProducts,
            'DealProducts'=>$DealProducts,
            'Deal'=>$Deal,
            'Categories' => $Categories,
            'SubCategories'=>$SubCategories,
            'Brands' => $Brands,
            'Models' => $Models,
            'Banners' => $Banners,
            'AllBanners' => $AllBanners,
            'Shops' => $Shops,
            'States' => $States
        ]);
    }



    
    public function load_more_top_selling($length)
    {
        $Products = $this->loadMoreProducts('num_of_sale', $length);
        return response()->json(['Products' => $Products]);
    }
    
    public function load_more_trending($length)
    {
        $Products = $this->loadMoreProducts('average_rating', $length);
        return response()->json(['Products' => $Products]);
    }
    
    public function load_more_featured($length)
    {
        $Products = $this->loadMoreProducts('id', $length)->where('featured', 1);
        return response()->json(['Products' => $Products]);
    }
    

    
}
