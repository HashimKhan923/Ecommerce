<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Models;
use App\Models\Brand;
use App\Models\Blog;


class SiteMapController extends Controller
{
    public function index()
    {
      $products = Product::select('id', 'name')->get();
      $categories = Category::with('sub_category:id,name,category_id')->select('id', 'name')->get();
      $sub_categories = SubCategory::select('id', 'name')->get();
      $models = Models::select('id', 'name')->get();
      $blogs = Blog::select('id', 'title')->get();
      $brands = Brand::with('model:id,name,brand_id')->select('id', 'name')->get();

      return response()->json(['products'=> $products,'categories'=> $categories,'sub_categories'=> $sub_categories,'models'=> $models,'brands'=> $brands,'blogs'=> $blogs]);
    }
}
