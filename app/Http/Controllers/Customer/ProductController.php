<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index($id)
    {
        $Products = Product::where('user_id',$id)->get();

        return response()->json(['Products'=>$Products]);
    }

    public function create(Request $request)
    {
        $new = new Product();
        $new->name = $request->name;
        $new->added_by = 'customer';
        $new->user_id = $request->user_id;
        $new->category_id = $request->category_id;
        $new->year = $request->year;
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
        $new->variant_product = $request->variant_product;
        $new->colors = $request->colors;
        $new->sizes = $request->sizes;
        $new->todays_deal = $request->todays_deal;
        $new->stock = $request->stock;
        $new->unit = $request->unit;
        $new->weight = $request->weight;
        $new->min_qty = $request->min_qty;
        $new->low_stock_quantity = $request->low_stock_quantity;
        $new->discount = $request->discount;
        $new->discount_type = $request->discount_type;
        $new->discount_start_date = $request->discount_start_date;
        $new->discount_end_date = $request->discount_end_date;
        $new->tax = $request->tax;
        $new->tax_type = $request->tax_type;
        $new->shipping_type = $request->shipping_type;
        $new->shipping_cost = $request->shipping_cost;
        $new->is_quantity_multiplied = $request->is_quantity_multiplied;
        $new->est_shipping_days = $request->est_shipping_days;
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
        $new->pdf = $request->pdf;
        $new->slug = $request->slug;
        $new->refundable = $request->refundable;
        $new->rating = $request->rating;
        $new->barcode = $request->barcode;
        $new->digital = $request->digital;
        $new->file_name = $request->file_name;
        $new->file_path = $request->file_path;
        $new->external_link = $request->external_link;
        $new->external_link_btn = $request->external_link_btn;
        $new->wholesale_product = $request->wholesale_product;
        $new->save();
        

    }


    public function update(Request $request)
    {
        $update = Product::where('id',$request->id)->first();
        $update->name = $request->name;
        $update->added_by = 'customer';
        $update->added_by = $request->added_by;
        $update->user_id = $request->user_id;
        $update->category_id = $request->category_id;
        $update->year = $request->year;
        $update->brand_id = $request->brand_id;
        if($request->file('photos'))
        {


           foreach($update->photos as $photosList)
           {
            $DeletePhotos = $photosList;
            if (Storage::exists($DeletePhotos))
            {
                Storage::delete($DeletePhotos);
            }
      
           }     


            foreach($request->photos as $photos)
            {
                $file= $photos;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $ProductGallery[] = $filename;
                
            }

            $update->photos = $ProductGallery;

        }

        if($request->file('thumbnail_img'))
        {

            $DeletePhoto = $update->thumbnail_img;
            if (Storage::exists($DeletePhoto))
            {
                Storage::delete($DeletePhoto);
            }


                $file= $request->thumbnail_img;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $update->thumbnail_img = $filename;
        }
        $update->tags = $request->tags;
        $update->description = $request->description;
        $update->price = $request->price;
        $update->variant_product = $request->variant_product;
        $update->colors = $request->colors;
        $update->sizes = $request->sizes;
        $update->todays_deal = $request->todays_deal;
        $update->stock = $request->stock;
        $update->unit = $request->unit;
        $update->weight = $request->weight;
        $update->min_qty = $request->min_qty;
        $update->low_stock_quantity = $request->low_stock_quantity;
        $update->discount = $request->discount;
        $update->discount_type = $request->discount_type;
        $update->discount_start_date = $request->discount_start_date;
        $update->discount_end_date = $request->discount_end_date;
        $update->tax = $request->tax;
        $update->tax_type = $request->tax_type;
        $update->shipping_type = $request->shipping_type;
        $update->shipping_cost = $request->shipping_cost;
        $update->is_quantity_multiplied = $request->is_quantity_multiplied;
        $update->est_shipping_days = $request->est_shipping_days;
        $update->num_of_sale = $request->num_of_sale;
        $update->meta_title = $request->meta_title;
        $update->meta_description = $request->meta_description;
        if($request->file('meta_img'))
        {

            $DeletePhoto = $update->meta_img;
            if (Storage::exists($DeletePhoto))
            {
                Storage::delete($DeletePhoto);
            }


                $file= $request->meta_img;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $update->meta_img = $filename;
        }
        $update->pdf = $request->pdf;
        $update->slug = $request->slug;
        $update->refundable = $request->refundable;
        $update->rating = $request->rating;
        $update->barcode = $request->barcode;
        $update->digital = $request->digital;
        $update->file_name = $request->file_name;
        $update->file_path = $request->file_path;
        $update->external_link = $request->external_link;
        $update->external_link_btn = $request->external_link_btn;
        $update->wholesale_product = $request->wholesale_product;
        $update->save();
        

    }

    public function delete($id)
    {
        $file = Product::find($id);


        foreach($file->photos as $photosList)
        {
         $DeletePhotos = $photosList;
         if (Storage::exists($DeletePhotos))
         {
             Storage::delete($DeletePhotos);
         }
   
        }  





        $ProductThumbnail = $file->thumbnail_img;
      if (Storage::exists($ProductThumbnail))
      {
          Storage::delete($ProductThumbnail);
      }

      $ProductMetaImage = $file->meta_img;
      if (Storage::exists($ProductMetaImage))
      {
          Storage::delete($ProductMetaImage);
      }

      $file->delete();

        $response = ['status'=>true,"message" => "Product Deleted Successfully!"];
        return response($response, 200);
    }
}
