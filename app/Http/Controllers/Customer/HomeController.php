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
            'discount', 'tax', 'shipping', 'shop.shop_policy', 'reviews.user', 'product_varient'
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
        $Categories = Category::with(['sub_category'])
        ->withCount('product')
        ->where('is_active', 1)
        ->orderByDesc('product_count')
        ->get();
    
    // Fetch products for each category separately
    // $Categories->each(function ($category) {
    //     $category->setRelation('product', $category->product()
    //         ->with([
    //             'user', 'category', 'sub_category', 'brand', 'model', 'stock',
    //             'product_gallery' => function ($query) {
    //                 $query->orderBy('order', 'asc');
    //             },
    //             'discount', 'tax', 'shipping', 'deal.deal_product',
    //             'wholesale', 'shop.shop_policy', 'reviews.user', 'product_varient'
    //         ])
    //         ->where('published', 1)
    //         ->whereHas('stock', function ($query) {
    //             $query->where('stock', '>', 0);
    //         })
    //         ->whereHas('shop', function ($query) {
    //             $query->where('status', 1);
    //         })
    //         ->orderByRaw('featured DESC')
    //         ->take(10)
    //         ->get());
    // });
        $Brands = Brand::with('model', 'product')->withCount('product')->where('is_active', 1)->orderByDesc('product_count')->get();
        $Banners = Banner::where('status', 1)->get();
        $States = State::where('status', 1)->get();
        $SubCategories = SubCategory::with('category')->where('is_active', 1)->get();
        $Models = Models::where('is_active',1)->get();
        $AllBanners = AllBanner::where('status', 1)->get();
        $Shops = Shop::with('seller', 'shop_policy', 'product.shop', 'product.product_gallery',
            'product.category','product.sub_category','product.brand', 'product.model', 'product.stock',
            'product.product_varient', 'product.reviews.user', 'product.tax')->where('status',1)->get();
    
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
            }, 'discount', 'tax', 'shipping', 'shop.shop_policy', 'reviews.user', 'product_varient'
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
