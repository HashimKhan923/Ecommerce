<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Discount;
use App\Models\Shipping;
use App\Models\Stock;
use App\Models\Tax;
use App\Models\WholesaleProduct;
use App\Models\DealProduct;
use App\Models\ProductVarient;
use App\Models\ProductGallery;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    public function index()
    {
        $Products = Product::with(['user','category','sub_category','brand','stock','product_gallery' => function($query) {
            $query->orderBy('order', 'asc');
        },'discount','tax','shipping','deal','shop','reviews','product_varient'])->get();

        return response()->json(['Products'=>$Products]);
    }


    private function loadMoreProducts($start, $length, $shopId, $status, $isFeatured, $searchValue, $dealId)
    {
       
        $query = Product::with([
            'user',
            'category',
            'sub_category',
            'brand',
            'model',
            'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            },
            'discount',
            'tax',
            'shipping',
            'deal',
            'wholesale',
            'shop.shop_policy',
            'reviews',
            'product_varient'
        ]);
    

        if ($shopId != 0) {
            $query->where('shop_id', $shopId);
        }

        if ($searchValue != 0) {

            $stopWords = ['for', 'the', 'a', 'and', 'of', 'to', 'on', 'in'];
            $searchWords = explode(' ', strtolower($searchValue));
            $keywords = array_diff($searchWords, $stopWords); // Remove stop words

            $query->where(function ($subQuery) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $subQuery->where(function ($subQuery) use ($keyword) {
                        $subQuery->where('sku', 'LIKE', "%{$keyword}%")
                            ->orWhere('name', 'LIKE', "%{$keyword}%")  // Product name
                            ->orWhereJsonContains('tags', $keyword)    // Tags
                            ->orWhereJsonContains('start_year', $keyword)
                            ->orWhereHas('shop', function ($subQuery) use ($keyword) {
                                $subQuery->where('name', 'LIKE', "%{$keyword}%");   // Shop name
                            })
                            ->orWhereHas('brand', function ($subQuery) use ($keyword) {
                                $subQuery->where('name', 'LIKE', "%{$keyword}%");   // Brand name (Make)
                            })
                            ->orWhereHas('model', function ($subQuery) use ($keyword) {
                                $subQuery->where('name', 'LIKE', "%{$keyword}%");   // Model name
                            })
                            ->orWhereHas('category', function ($subQuery) use ($keyword) {
                                $subQuery->where('name', 'LIKE', "%{$keyword}%");   // Category name
                            })
                            ->orWhereHas('sub_category', function ($subQuery) use ($keyword) {
                                $subQuery->where('name', 'LIKE', "%{$keyword}%");   // Sub-category name
                            });
                    });
                }
            });
            
        }
    
       
        if ($status != 10) {
            $query->where('published', $status); 
        }
    
       
        if ($isFeatured != 10) {
            $query->where('featured', $isFeatured);  
        }

        if ($isFeatured != 10) {
            $query->where('featured', $isFeatured);  
        }

        if ($dealId != 10) {
            $query->where('deal_id', 4);  
        }

    
        return $query->orderBy('id', 'desc')
                     ->skip($start)
                     ->take($length)
                     ->get();
    }
    
    public function load_more($start, $length, $shopId, $status, $isFeatured, $searchValue, $dealId)
    {
        
        
        if ($start < 0) {
            $start = 0;
        }
    
        $products = $this->loadMoreProducts($start, $length, $shopId, $status, $isFeatured, $searchValue, $dealId);
        return response()->json(['Products' => $products]);
    }

    public function detail($product_id)
    {
        $data = Product::with([
            'user',
            'category',
            'sub_category',
            'brand',
            'model',
            'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            },
            'discount',
            'tax',
            'shipping',
            'shop',
            'deal',
            'product_varient'
        ])->where('id', $product_id)->first();

        return response()->json(['data' => $data]);
    }







    public function delete($id)
    {
        $file = Product::find($id);

        $gallery = ProductGallery::where('product_id',$id)->get();
        foreach($gallery as $item)
        {
            $checkCount = ProductGallery::where('image',$item->image)->count();

            if($checkCount < 2)
            {
                $fileToDelete = public_path('ProductGallery/'.$item->image);
                
                if (file_exists($fileToDelete) && is_file($fileToDelete)) {
                    unlink($fileToDelete);
                } 
            }


                unlink(public_path('ProductGallery/'.$item->image));
        }
        
        $varients = ProductVarient::where('product_id',$id)->get();
        foreach($varients as $item)
        {
            $checkCount = ProductVarient::where('image',$item->image)->count();

            if($checkCount < 2)
            {
                $fileToDelete = public_path('ProductVarient/'.$item->image);
                
                if (file_exists($fileToDelete) && is_file($fileToDelete)) {
                    unlink($fileToDelete);
                } 
            }
        }


        $file->delete();

        $response = ['status'=>true,"message" => "Product Deleted Successfully!"];
        return response($response, 200);
    }




    public function multi_delete(Request $request)
    {
        $data = Product::whereIn('id',$request->ids)->get();

        foreach($data as $item)
        {
            $gallery = ProductGallery::where('product_id',$item->id)->get();
            foreach($gallery as $item1)
            {
                
                $checkCount = ProductGallery::where('image',$item1->image)->count();

                if($checkCount < 2)
                {
                    $fileToDelete = public_path('ProductGallery/'.$item1->image);

                        if (file_exists($fileToDelete) && is_file($fileToDelete)) {
                            unlink($fileToDelete);
                        } 
                }
            }
            
            $varients = ProductVarient::where('product_id',$item->id)->get();
            foreach($varients as $item2)
            {
                $checkCount = ProductVarient::where('image',$item2->image)->count();

                if($checkCount < 2)
                {
                    $fileToDelete = public_path('ProductVarient/'.$item2->image);

                    if (file_exists($fileToDelete) && is_file($fileToDelete)) {
                        unlink($fileToDelete);
                    } 
                }
            }
    

    

            $item->delete();
        }

        

        $response = ['status'=>true,"message" => "Products Deleted Successfully!"];
        return response($response, 200);
    }





    public function is_approved($id)
    {
        $is_approved = Product::where('id',$id)->first();

        if($is_approved->approved == 0)
        {
            $is_approved = 1;
        }
        else
        {
            $is_approved = 0;
        }

        $is_approved->save();

        $response = ['status'=>true,"message" => "Status Changed Successfully!"];
        return response($response, 200);
    }

    public function is_published($id)
    {
        $is_published = Product::where('id',$id)->first();

        if($is_published->published == 0)
        {
            $is_published->published = 1;
        }
        else
        {
            $is_published->published = 0;
        }

        $is_published->save();

        $response = ['status'=>true,"message" => "Status Changed Successfully!"];
        return response($response, 200);
    }

    public function is_featured($id)
    {
        $is_featured = Product::where('id',$id)->first();

        if($is_featured->featured == 0)
        {
            $is_featured->featured = 1;
        }
        else
        {
            $is_featured->featured = 0;
        }

        $is_featured->save();

        $response = ['status'=>true,"message" => "Status Changed Successfully!"];
        return response($response, 200);
    }
}
