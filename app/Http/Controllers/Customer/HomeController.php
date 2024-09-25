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

class HomeController extends Controller
{
    public function index()
    {
        $Products = Product::with([
            'user', 'category','sub_category','brand', 'model', 'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            },
            'discount', 'tax', 'shipping','deal','shop.shop_policy', 'reviews.user', 'product_varient'
        ])->where('published', 1)->orderBy('id', 'desc')->whereHas('stock', function ($query) {
            $query->where('stock', '>', 0);
        })->whereHas('shop', function ($query) {
            $query->where('status', 1);
        })->take(24);
    
        $TopSelling = Product::with([
            'user', 'category','sub_category','brand', 'model', 'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            },
            'discount', 'tax', 'shipping',
            'shop.shop_policy', 'reviews.user', 'product_varient'
        ])->where('published', 1)->whereHas('stock', function ($query) {
            $query->where('stock', '>', 0);
        })->whereHas('shop', function ($query) {
            $query->where('status', 1);
        })->inRandomOrder()->orderBy('num_of_sale', 'desc')
        ->take(10);

    
        
        $TrendingProducts = Product::with([
            'user', 'category','sub_category','brand', 'model', 'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            },
            'discount', 'tax', 'shipping', 'shop.shop_policy', 'reviews.user', 'product_varient'
        ])->where('published', 1)->whereHas('stock', function ($query) {
            $query->where('stock', '>', 0);
        })->whereHas('shop', function ($query) {
            $query->where('status', 1);
        })->inRandomOrder()->orderBy('average_rating', 'desc')
        ->take(10);
    
        $FeaturedProducts = clone $Products;
        $FeaturedProducts->where('featured', 1)->take(10);
    
        $allProducts = $Products->get();
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
        $Shops = Shop::with('seller', 'shop_policy')->where('status',1)->get();
        $Deals = Deal::with('deal_shop.shop')
        ->where('discount_start_date', '<=', now())
        ->where('discount_end_date', '>=', now())
        ->get();

        // $dealProduct = Deal::with(['deal_product.product' => function ($query) use ($seller_id) {
        //     $query->where('user_id', $seller_id);
        // },
        // 'deal_product.product.product_single_gallery',
        // 'deal_product.product.shop'])->get();
    
        return response()->json([
            'Products' => $allProducts,
            'FeaturedProducts' => $FeaturedProducts->get(),
            'TopSelling' => $TopSelling->get(),
            'TrendingProducts' => $TrendingProducts->get(),
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


    private function loadMoreProducts($orderBy, $length)
    {
        return Product::with([
            'user', 'category','sub_category','brand', 'model', 'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            }, 'discount', 'tax', 'shipping','deal','shop.shop_policy', 'reviews.user', 'product_varient'
        ])->where('published', 1)->orderBy($orderBy, 'desc')->whereHas('stock', function ($query) {
            $query->where('stock', '>', 0);
        })->whereHas('shop', function ($query) {
            $query->where('status', 1);
        })->skip($length)->take(24)->get();
    }
    
    public function load_more($length)
    {
        $Products = $this->loadMoreProducts('id', $length);
        return response()->json(['Products' => $Products]);
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
