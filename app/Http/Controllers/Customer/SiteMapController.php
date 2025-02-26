<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Model;
use App\Models\Brand;

class SiteMapController extends Controller
{
    public function product_ids()
    {
      $products = Product::pluck('id','name');
      $categories = Category::pluck('id','name');
      $sub_categories = SubCategory::pluck('id','name');
      $models = Model::pluck('id','name');
      $brands = Brand::pluck('id','name');

      return response()->json(['products'=> $products,'categories'=> $categories,'sub_categories'=> $sub_categories,'models'=> $models,'brands'=> $brands]);
    }
}
