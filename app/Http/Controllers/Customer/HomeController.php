<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\SubscribeUser;
use App\Models\Banner;
use App\Models\AllBanner;
use App\Models\State;
use App\Models\Shop;
use DB;

class HomeController extends Controller
{
    public function index()
    {
        $Products = Product::with([
            'user', 'category', 'brand', 'model', 'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            },
            'discount', 'tax', 'shipping', 'deal.deal_product',
            'wholesale', 'shop.shop_policy', 'reviews.user', 'product_varient'
        ])->where('published', 1)->orderBy('id', 'desc')->take(24);
    
        $TopSelling = clone $Products;
        $TopSelling->orderBy('num_of_sale', 'desc')->take(10);
    
        $TrendingProducts = clone $Products;
        $TrendingProducts->orderBy('average_rating', 'desc')->take(10);
    
        $FeaturedProducts = clone $Products;
        $FeaturedProducts->where('featured', 1)->take(10);
    
        $allProducts = $Products->get();
        $Categories = Category::where('is_active', 1)->get();
        $Brands = Brand::with('model')->where('is_active', 1)->get();
        $Banners = Banner::where('status', 1)->get();
        $AllBanners = AllBanner::where('status', 1)->get();
        $Shops = Shop::with('seller', 'shop_policy', 'product.shop', 'product.product_gallery',
            'product.category', 'product.brand', 'product.model', 'product.stock',
            'product.product_varient', 'product.reviews.user', 'product.tax')->get();
    
        return response()->json([
            'Products' => $allProducts,
            'FeaturedProducts' => $FeaturedProducts->get(),
            'TopSelling' => $TopSelling->get(),
            'TrendingProducts' => $TrendingProducts->get(),
            'Categories' => $Categories,
            'Brands' => $Brands,
            'Banners' => $Banners,
            'AllBanners' => $AllBanners,
            'Shops' => $Shops
        ]);
    }


    public function load_more($length)
    {
        $Products = Product::with([
            'user',
            'category',
            'brand',
            'model',
            'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            },
            'discount',
            'tax',
            'shipping',
            'deal.deal_product',
            'wholesale',
            'shop.shop_policy',
            'reviews.user',
            'product_varient'
        ])->where('published',1)->orderBy('id', 'desc')->skip($length)->take(24)->get();

        return response()->json(['Products'=>$Products]);

    }

    public function load_more_top_selling($length)
    {
        $Products = Product::with([
            'user',
            'category',
            'brand',
            'model',
            'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            },
            'discount',
            'tax',
            'shipping',
            'deal.deal_product',
            'wholesale',
            'shop.shop_policy',
            'reviews.user',
            'product_varient'
        ])->where('published',1)->orderBy('num_of_sale', 'desc')->skip($length)->take(24)->get();

        return response()->json(['Products'=>$Products]);

    }

    public function load_more_trending($length)
    {
        $Products = Product::with([
            'user',
            'category',
            'brand',
            'model',
            'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            },
            'discount',
            'tax',
            'shipping',
            'deal.deal_product',
            'wholesale',
            'shop.shop_policy',
            'reviews.user',
            'product_varient'
        ])->where('published',1)->orderBy('average_rating', 'desc')->skip($length)->take(24)->get();

        return response()->json(['Products'=>$Products]);

    }

    public function load_more_featured($length)
    {
        $Products = Product::with([
            'user',
            'category',
            'brand',
            'model',
            'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            },
            'discount',
            'tax',
            'shipping',
            'deal.deal_product',
            'wholesale',
            'shop.shop_policy',
            'reviews.user',
            'product_varient'
        ])->where('published',1)->where('featured',1)->orderBy('id', 'desc')->skip($length)->take(24)->get();

        return response()->json(['Products'=>$Products]);

    }

    
}
