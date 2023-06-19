<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Discount;
use App\Models\Shipping;
use App\Models\Stock;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $Products = Product::with('user','category','brand','stock','discount','tax','shipping')->get();

        return response()->json(['Products'=>$Products]);
    }

    public function create(Request $request)
    {
        $new = new Product();
        $new->name = $request->name;
        $new->added_by = 'admin';
        $new->user_id = $request->user_id;
        $new->category_id = $request->category_id;
        $new->weight = $request->weight;
        $new->sku = $request->sku;
        $new->brand_id = $request->brand_id;
        if($request->file('photos'))
        {
            
            foreach($request->photos as $photo)
            { 
                $file= $photo;
                $filename= date('YmdHis').$file->getClientOriginalName();
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
        $new->num_of_sale = $request->num_of_sale;
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

        if($request->discount != null)
        {
            $discount = new Discount();
            $discount->product_id = $new->id;
            $discount->discount = $request->discount;
            $discount->discount_type = $request->discount_type;
            $discount->discount_start_date = Carbon::parse($request->discount_start_date);
            $discount->discount_end_date = Carbon::parse($request->discount_end_date);
            $discount->save();
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
            $shipping->shipping_type = $request->shipping_type;
            $shipping->shipping_cost = $request->shipping_cost;
            $shipping->est_shipping_days = $request->est_shipping_days;
            $shipping->save();
        }
        

    }


    public function update(Request $request)
    {
        $update = Product::where('id',$request->id)->first();
        $update->name = $request->name;
        $update->added_by = 'admin';
        $update->user_id = $request->user_id;
        $update->category_id = $request->category_id;
        $update->weight = $request->weight;
        $update->sku = $request->sku;
        $update->brand_id = $request->brand_id;
        if($request->file('photos'))
        {
            
            foreach($request->photos as $photo)
            { 

                $file= $photo;
                $filename= date('YmdHis').$file->getClientOriginalName();
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
        $update->num_of_sale = $request->num_of_sale;
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
        $update->rating = $request->rating;
        $update->save();



        if($request->stock != null)
        {
            $stock = Stock::where('product_id',$update->id)->first();
            $stock->product_id = $update->id;
            $stock->stock = $request->stock;
            $stock->min_stock = $request->min_stock;
            $stock->save();
        }

        if($request->discount != null)
        {
            $discount = Discount::where('product_id',$update->id)->first();
            $discount->product_id = $update->id;
            $discount->discount = $request->discount;
            $discount->discount_type = $request->discount_type;
            $discount->discount_start_date = Carbon::parse($request->discount_start_date);
            $discount->discount_end_date = Carbon::parse($request->discount_end_date);
            $discount->save();
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
            $shipping->shipping_type = $request->shipping_type;
            $shipping->shipping_cost = $request->shipping_cost;
            $shipping->est_shipping_days = $request->est_shipping_days;
            $shipping->save();
        }
        

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
