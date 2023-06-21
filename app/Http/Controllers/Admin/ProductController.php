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
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $Products = Product::with('user','category','brand','stock','discount','tax','shipping','deal','wholesale')->get();

        return response()->json(['Products'=>$Products]);
    }

    public function admin_products()
    {
        $Products = Product::with('user','category','brand','stock','discount','tax','shipping','deal','wholesale')->where('added_by','admin')->get();

        return response()->json(['Products'=>$Products]);
    }

    public function seller_products()
    {
        $Products = Product::with('user','category','brand','stock','discount','tax','shipping','deal','wholesale')->where('added_by','seller')->get();

        return response()->json(['Products'=>$Products]);
    }



    public function create(Request $request)
    {
        $new = new Product();
        $new->name = $request->name;
        $new->added_by = 'admin';
        $new->user_id = $request->user_id;
        $new->category_id = $request->category_id;

        if($request->deal_id != null)
        {

                $new->deal_id = $request->deal_id;

                $discount = new Discount();
                $discount->product_id = $new->id;
                $discount->discount = $request->discount;
                $discount->discount_type = $request->discount_type;
                $discount->save();
            
        }

        $new->weight = $request->weight;
        $new->unit = $request->unit;
        $new->sku = $request->sku;
        $new->brand_id = $request->brand_id;
        $new->model = $request->model;

        if ($request->file('photos')) {
            $ProductGallery = array(); // Initialize the array
        
            foreach ($request->file('photos') as $photo) {
                $file = $photo;
                $filename = date('YmdHis') . $file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $ProductGallery[] = $filename;
            }
        
            $new->photos = $ProductGallery;
        }

        if($request->file('thumbnail_img'))
        {
                $file= $request->thumbnail_img;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $new->thumbnail_img = $filename;
        }
        $new->tags = $request->tags;
        $new->description = $request->description;
        $new->price = $request->price;
        $new->colors = $request->colors;
        $new->sizes = $request->sizes;
        $new->cash_on_delivery = $request->cash_on_delivery;
        $new->featured = $request->featured;
        $new->todays_deal = $request->todays_deal;
        $new->meta_title = $request->meta_title;
        $new->meta_description = $request->meta_description;
        if($request->file('meta_img'))
        {
                $file= $request->meta_img;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $new->meta_img = $filename;
        }
        $new->slug = $request->slug;
        $new->sku = $request->sku;
        $new->save();

        if($request->stock != null)
        {
            $stock = new Stock();
            $stock->product_id = $new->id;
            $stock->stock = $request->stock;
            $stock->min_stock = $request->min_stock;
            $stock->save();
        }


        if($request->tax != null)
        {
            $tax = new Tax();
            $tax->product_id = $new->id;
            $tax->tax = $request->tax;
            $tax->tax_type = $request->tax_type;
            $tax->save();
        }

        

        if($request->shipping_type != null)
        {
            $shipping = new Shipping();
            $shipping->product_id = $new->id;
            $shipping->shipping_cost = $request->shipping_cost;
            $shipping->is_qty_multiply = $request->is_qty_multiply;
            $shipping->est_shipping_days = $request->est_shipping_days;
            $shipping->save();
        }

        if($request->wholesale_price != null)
        {
            foreach($request->wholesale_price as $price)
            {
                $wholesale = new WholesaleProduct();
                $wholesale->product_id = $new->id;
                $wholesale->wholesale_price = $price;
                $wholesale->wholesale_min_qty = $request->wholesale_min_qty;
                $wholesale->wholesale_max_qty = $request->wholesale_max_qty;
                $wholesale->save();               
            }
        }

        $response = ['status'=>true,"message" => "Product Added Successfully!"];
        return response($response, 200);
        

    }


    public function update(Request $request)
    {
        $update = Product::where('id',$request->id)->first();
        $update->name = $request->name;
        $update->added_by = 'admin';
        $update->user_id = $request->user_id;
        $update->category_id = $request->category_id;

        if($request->deal_id != null)
        {

                $update->deal_id = $request->deal_id;

                $discount = Discount::where('product_id',$update->id)->first();
                $discount->product_id = $update->id;
                $discount->discount = $request->discount;
                $discount->discount_type = $request->discount_type;
                $discount->save();
            
        }

        $update->weight = $request->weight;
        $update->unit = $request->unit;
        $update->sku = $request->sku;
        $update->brand_id = $request->brand_id;
        $update->model = $request->model;
        
        if ($request->file('photos')) {
            $ProductGallery = array(); // Initialize the array
        
            foreach ($request->file('photos') as $photo) {
                $file = $photo;
                $filename = date('YmdHis') . $file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $ProductGallery[] = $filename;
            }
        
            $update->photos = $ProductGallery;
        }

        if($request->file('thumbnail_img'))
        {
                $file= $request->thumbnail_img;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $update->thumbnail_img = $filename;
        }
        $update->tags = $request->tags;
        $update->description = $request->description;
        $update->price = $request->price;
        $update->colors = $request->colors;
        $update->sizes = $request->sizes;
        $update->cash_on_delivery = $request->cash_on_delivery;
        $update->featured = $request->featured;
        $update->todays_deal = $request->todays_deal;
        $update->meta_title = $request->meta_title;
        $update->meta_description = $request->meta_description;
        if($request->file('meta_img'))
        {
                $file= $request->meta_img;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $update->meta_img = $filename;
        }
        $update->slug = $request->slug;
        $update->sku = $request->sku;
        $update->save();

        if($request->stock != null)
        {
            $stock = Stock::where('product_id',$update->id)->first();
            $stock->product_id = $update->id;
            $stock->stock = $request->stock;
            $stock->min_stock = $request->min_stock;
            $stock->save();
        }


        if($request->tax != null)
        {
            $tax = Tax::where('product_id',$update->id)->first();
            $tax->product_id = $update->id;
            $tax->tax = $request->tax;
            $tax->tax_type = $request->tax_type;
            $tax->save();
        }

        if($request->shipping_type != null)
        {
            $shipping = Shipping::where('product_id',$update->id)->first();
            $shipping->product_id = $update->id;
            $shipping->shipping_cost = $request->shipping_cost;
            $shipping->is_qty_multiply = $request->is_qty_multiply;
            $shipping->est_shipping_days = $request->est_shipping_days;
            $shipping->save();
        }

        if($request->wholesale_price != null)
        {
            foreach($request->wholesale_price as $price)
            {
                $wholesale = WholesaleProduct::where('product_id',$update->id)->first();
                $wholesale->product_id = $update->id;
                $wholesale->wholesale_price = $price;
                $wholesale->wholesale_min_qty = $request->wholesale_min_qty;
                $wholesale->wholesale_max_qty = $request->wholesale_max_qty;
                $wholesale->save();               
            }
        }
        
        $response = ['status'=>true,"message" => "Product Updated Successfully!"];
        return response($response, 200);
    }

    public function delete($id)
    {
        $file = Product::find($id);


        foreach($file->photos as $photosList)
        {
         $DeletePhotos = 'app/public'.$photosList;
         if (Storage::exists($DeletePhotos))
         {
             Storage::delete($DeletePhotos);
         }
   
        }  





        $ProductThumbnail = 'app/public'.$file->thumbnail_img;
      if (Storage::exists($ProductThumbnail))
      {
          Storage::delete($ProductThumbnail);
      }

      $ProductMetaImage = 'app/public'.$file->meta_img;
      if (Storage::exists($ProductMetaImage))
      {
          Storage::delete($ProductMetaImage);
      }

      $file->delete();

        $response = ['status'=>true,"message" => "Product Deleted Successfully!"];
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
            $is_published = 1;
        }
        else
        {
            $is_published = 0;
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
            $is_featured = 1;
        }
        else
        {
            $is_featured = 0;
        }

        $is_featured->save();

        $response = ['status'=>true,"message" => "Status Changed Successfully!"];
        return response($response, 200);
    }
}
