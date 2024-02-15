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

        // $package = SubscribeUser::where('end_time', '<=', now())->first();

        // if ($package) {
        //     Product::where('user_id', $package->user_id)->where('featured', 1)->update(['featured' => 0]);
        //     $package->delete();
        // }

        //$Products = Product::with('user','category','brand','model','stock','product_gallery','discount','tax','shipping','deal.deal_product','wholesale','shop','reviews.user','product_varient')->where('published',1)->get();
		//$Productss = Product::with('user','category','brand','model','stock',['product_gallery'=>function($query){return $query->orderByAsc('order');}],'discount','tax','shipping','deal.deal_product','wholesale','shop','reviews.user','product_varient')->where('published',1)->get();
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
    'shop',
    'reviews.user',
    'product_varient'
])->where('published',1)->orderBy('id', 'desc')->take(24)->get();
		
        $TopSelling = Product::with(['user', 'category', 'brand', 'model', 'stock',
        'product_gallery' => function($query) {
            $query->orderBy('order', 'asc');
        }, 'discount', 'tax', 'shipping', 'deal.deal_product', 'wholesale', 'shop', 'reviews.user', 'product_varient'])
        ->where('published', 1)
        ->orderBy('num_of_sale', 'desc')
        ->where('published',1)->take(10)->get();

        $TrendingProducts = Product::with(['user', 'category', 'brand', 'model', 'stock',
        'product_gallery' => function($query) {
            $query->orderBy('order', 'asc');
        }, 'discount', 'tax', 'shipping', 'deal.deal_product', 'wholesale', 'shop', 'reviews.user', 'product_varient'])
        ->orderBy('average_rating', 'desc')
        ->where('published',1)->orderBy('id', 'desc')->take(10)->get();

        $FeaturedProducts = Product::with(['user', 'category', 'brand', 'model', 'stock',
        'product_gallery' => function($query) {
            $query->orderBy('order', 'asc');
        }, 'discount', 'tax', 'shipping', 'deal.deal_product', 'wholesale', 'shop', 'reviews.user', 'product_varient'])
        ->where('featured',1)
        ->where('published',1)->orderBy('id', 'desc')->take(10)->get();

        $Categories = Category::where('is_active',1)->get();
        $Brands = Brand::with('model')->where('is_active',1)->get();
        $Banners = Banner::where('status',1)->get();
        $AllBanners = AllBanner::where('status',1)->get();
        // $States = State::all();
        $Shops = Shop::with('seller','product.shop','product.product_gallery','product.category','product.brand','product.model','product.stock','product.product_varient','product.reviews.user','product.tax')->get();

        return response()->json(['Products'=>$Products,'FeaturedProducts'=>$FeaturedProducts,'TopSelling'=>$TopSelling,'TrendingProducts'=>$TrendingProducts,'Categories'=>$Categories,'Brands'=>$Brands,'Banners'=>$Banners,'AllBanners'=>$AllBanners,'Shops'=>$Shops]);
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
            'shop',
            'reviews.user',
            'product_varient'
        ])->where('published',1)->orderBy('id', 'desc')->skip($length)->take(12)->get();

        return response()->json(['Products'=>$Products]);

    }

    
}
