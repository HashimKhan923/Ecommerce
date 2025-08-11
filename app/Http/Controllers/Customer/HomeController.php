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
use App\Models\AiTrendingProduct;
use App\Models\Models;
use App\Models\Deal;
use DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $FeaturedProducts = Product::with([
            'stock','wishlistProduct',
            'product_gallery' => function ($query) {
                $query->orderBy('order', 'asc');
            },
            'discount','shop', 'reviews.user', 'product_varient'
        ])->where('published', 1)
        //   ->whereHas('stock', function ($query) {
        //       $query->where('stock', '>', 0);
        //   })
          ->whereHas('shop', function ($query) {
              $query->where('status', 1);
          })->where('featured', 1)
          ->orderByRaw('RAND()') 
          ->take(20)
          ->get();

          $trendingKeywords = AiTrendingProduct::pluck('names')->toArray();

        //   return $trendingKeywords;

            $trendingProducts = collect();

            foreach ($trendingKeywords as $keyword) {
                $matched = Product::with([
            'stock',
            'product_gallery' => function ($query) {
                $query->orderBy('order', 'asc');
            },
            'discount','shop', 'reviews.user', 'product_varient'
        ])->where('published', 1)

          ->whereHas('shop', function ($query) {
              $query->where('status', 1);
          })->where('name', 'like', '%' . $keyword . '%')
                    ->limit(2) // fetch 2 per keyword
                    ->get();

                $trendingProducts = $trendingProducts->merge($matched);
            }

            // Remove duplicate products
            $trendingProducts = $trendingProducts->unique('id')->take(20);
    
        $Categories = Category::with([
            'subCategories' => function ($query) {
                $query->withCount('product') 
                      ->orderBy('category_sub_category.order', 'asc'); 
            }
        ])
        ->whereHas('product')
        ->where('is_active', 1)
        ->orderBy('order', 'asc')
        ->get();
    

        $Brands = Brand::with('model')->withCount('product')->whereHas('product')->where('is_active', 1)->orderByDesc('product_count')->get();
        $Banners = Banner::where('status', 1)->get();
        $States = State::where('status', 1)->get();
        $SubCategories = SubCategory::with('category')->where('is_active', 1)->get();
        $Models = Models::where('is_active',1)->get();
        $AllBanners = AllBanner::where('status', 1)->get();
        $Shops = Shop::with('seller', 'shop_policy')
    ->where('status', 1)
    ->withCount('product') 
    ->get();



    
        return response()->json([
            'FeaturedProducts' => $FeaturedProducts,
            'Categories' => $Categories,
            'SubCategories'=>$SubCategories,
            'Brands' => $Brands,
            'TrendingProducts' => $matched,
            'Models' => $Models,
            'Banners' => $Banners,
            'AllBanners' => $AllBanners,
            'Shops' => $Shops,
            'States' => $States
        ]);
    }

    public function index1()
    {
        $FeaturedProducts = Product::with([
            'stock','wishlistProduct',
            'product_gallery' => function ($query) {
                $query->orderBy('order', 'asc');
            },
            'discount','shop', 'reviews.user', 'product_varient'
        ])->where('published', 1)
        //   ->whereHas('stock', function ($query) {
        //       $query->where('stock', '>', 0);
        //   })
          ->whereHas('shop', function ($query) {
              $query->where('status', 1);
          })->where('featured', 1)
          ->orderByRaw('RAND()') 
          ->take(20)
          ->get();
    
          $Categories = Category::select('categories.id', 'categories.name','categories.icon','categories.mobile_banner') // Fully qualify columns
          ->with([
              'subCategories' => function ($query) {
                  $query->select('sub_categories.id', 'sub_categories.name', 'sub_categories.icon','sub_categories.category_id')
                        ->withCount('product')
                        ->orderBy('category_sub_category.order', 'asc');
              }
          ])
          ->whereHas('product')
          ->where('is_active', 1)
          ->orderBy('order', 'asc')
          ->get();
      
    

        $Brands = Brand::select('id','name','logo','banner')->with('model')->withCount('product')->whereHas('product')->where('is_active', 1)->orderByDesc('product_count')->get();
        $Banners = Banner::select('id','mobile_link','mobile_image')->where('status', 1)->get();
        $States = State::where('status', 1)->get();
        $SubCategories = SubCategory::select('sub_categories.id', 'sub_categories.name', 'sub_categories.icon','sub_categories.category_id')
        ->with(['category' => function ($query) {
            $query->select('categories.id', 'categories.name','categories.icon','categories.mobile_banner');
        }])
        ->where('is_active', 1)
        ->orderBy('order', 'asc')
        ->get();        
        $Models = Models::select('id', 'name','logo','banner')->whereHas('product')->withCount('product')->orderByDesc('product_count')->where('is_active',1)->get();
        $AllBanners = AllBanner::select('id','mobile_link','mobile_image')->where('status', 1)->get();
        $Shops = Shop::with('seller', 'shop_policy')
        ->where('status', 1)
        ->whereHas('seller', function ($query) {
            $query->where('is_active', 1)->where('is_verify', 1); // or 1, depending on how you're storing status
        })
        ->withCount('product')
        ->get();



    
        return response()->json([
            'FeaturedProducts' => $FeaturedProducts,
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
