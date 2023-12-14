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
        $Products = Product::with('user','category','brand','stock','product_gallery','discount','tax','shipping','deal.deal_product','shop','reviews','product_varient')->get();

        return response()->json(['Products'=>$Products]);
    }

    public function admin_products()
    {
        $Products = Product::with('user','category','brand','stock','product_gallery','discount','tax','shipping','deal.deal_product','wholesale','shop','reviews','product_varient')->where('added_by','admin')->get();

        return response()->json(['Products'=>$Products]);
    }

    public function seller_products()
    {
        $Products = Product::with('user','category','brand','stock','product_gallery','discount','tax','shipping','deal.deal_product','wholesale','shop','reviews','product_varient')->where('added_by','seller')->get();

        return response()->json(['Products'=>$Products]);
    }



    public function create(Request $request)
    {
        
        
        $new = new Product();
        $new->name = $request->name;
        $new->added_by = 'admin';
        $new->user_id = $request->user_id;
        $new->category_id = $request->category_id;
        $new->height = $request->height;
        $new->weight = $request->weight;
        $new->lenght = $request->lenght;
        $new->start_year = $request->start_year;
        $new->end_year = $request->end_year;
        $new->make = $request->make;
        $new->unit = $request->unit;
        $new->sku = $request->sku;
        $new->condition = $request->condition;
        $new->brand_id = $request->brand_id;
        $new->model_id = $request->model_id;
        $new->deal_id = $request->deal_id;
        $new->tags = $request->tags;
        $new->description = $request->description;
        $new->price = $request->price;
        $new->shipping = $request->shipping;
        $new->featured = $request->featured;
        $new->meta_title = $request->meta_title;
        $new->meta_description = $request->meta_description;
        // if($request->file('meta_img'))
        // {
        //     $file= $request->meta_img;
        //     $filename= date('YmdHis').$file->getClientOriginalName();
        //     $file->move(public_path('ProductMetaImg'),$filename);

                    
        // $compressedImage = Image::make(public_path('ProductMetaImg') . '/' . $filename)
        // ->encode('jpg', 50); 

        
        // $compressedFilename = 'compressed_' . $filename;
        // $compressedImage->save(public_path('ProductMetaImg') . '/' . $compressedFilename);

        // unlink(public_path('ProductMetaImg/'.$filename));


        // $new->meta_img = $compressedFilename;
        // }
        $new->slug = $request->slug;
        $new->save();

        if ($request->photos) {
            foreach ($request->file('photos') as $image) {
                $gallery = new ProductGallery();
                $gallery->product_id = $new->id;   

                $filename = date('YmdHis') . $image->getClientOriginalName();

                $compressedImage = Image::make($image->getRealPath());
                
                $compressedImage->encode('webp')->save(public_path('ProductGallery') . '/' . $filename . '.webp');
                
                $gallery->image = $filename . '.webp';
                
                $gallery->save();
            }
        }

        if($request->varients != null)
        {
            
            foreach($request->varients as $item)
            {
                $varient = new ProductVarient();
                $varient->product_id = $new->id;
                $varient->color = $item['color'];
                $varient->size = $item['size'];
                $varient->bolt_pattern = $item['bolt_pattern'];
                $varient->price = $item['varient_price'];
                $varient->discount_price = $item['varient_discount_price'];
                $varient->sku = $item['varient_sku'];
                $varient->stock = $item['varient_stock'];
                // if($item->file('varient_image'))
                // {
                //         $file= $item->varient_image;
                //         $filename= date('YmdHis').$file->getClientOriginalName();
                //         $file->move(public_path('ProductVarient'),$filename);

                //         $compressedImage = Image::make(public_path('ProductVarient') . '/' . $filename)
                //         ->encode('jpg', 50); 
                
                        
                //         $compressedFilename = 'compressed_' . $filename;
                //         $compressedImage->save(public_path('ProductVarient') . '/' . $compressedFilename);
                
                //         unlink(public_path('ProductVarient/'.$filename));


                //         $varient->image = $compressedFilename;
                // }
                $varient->save();
            }

        }

        if($request->discount != null)
        {
            $discount = new Discount();
            $discount->product_id = $new->id;
            $discount->discount = $request->discount;
            $discount->discount_start_date = $request->discount_start_date;
            $discount->discount_end_date = $request->discount_end_date;
            $discount->discount_type = $request->discount_type;
            $discount->save();
        }

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


        if($request->deal_id != null)
        {
            $deal = new DealProduct();
            $deal->deal_id = $request->deal_id;
            $deal->product_id = $new->id;
            $deal->discount = $request->deal_discount;
            $deal->discount_type = $request->deal_discount_type;
            $deal->save();
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
        $update->height = $request->height;
        $update->weight = $request->weight;
        $update->lenght = $request->lenght;
        $update->start_year = $request->start_year;
        $update->end_year = $request->end_year;
        $update->make = $request->make;
        $update->unit = $request->unit;
        $update->sku = $request->sku;
        $update->condition = $request->condition;
        $update->brand_id = $request->brand_id;
        $update->model_id = $request->model_id;
        $update->deal_id = $request->deal_id;
        $update->tags = $request->tags;
        $update->description = $request->description;
        $update->price = $request->price;
        $update->shipping = $request->shipping;
        $update->featured = $request->featured;
        $update->todays_deal = $request->todays_deal;
        $update->meta_title = $request->meta_title;
        $update->meta_description = $request->meta_description;
        // if($request->file('meta_img'))
        // {
        //     if($update->meta_img)
        //     {
        //         unlink(public_path('ProductMetaImg/'.$update->meta_img));
        //     }

        //     $file= $request->meta_img;
        //     $filename= date('YmdHis').$file->getClientOriginalName();
        //     $file->move(public_path('ProductMetaImg'),$filename);

        //     $compressedImage = Image::make(public_path('ProductMetaImg') . '/' . $filename)
        //     ->encode('jpg', 50); 
    
            
        //     $compressedFilename = 'compressed_' . $filename;
        //     $compressedImage->save(public_path('ProductMetaImg') . '/' . $compressedFilename);
    
        //     unlink(public_path('ProductMetaImg/'.$filename));

        //     $update->meta_img = $compressedFilename;
        // }
        $update->slug = $request->slug;
        $update->save();


        if ($request->photos) {
            foreach ($request->file('photos') as $image) {
                $gallery = new ProductGallery();
                $gallery->product_id = $update->id;
            
                $filename = date('YmdHis') . $image->getClientOriginalName();

                $compressedImage = Image::make($image->getRealPath());
                
                $compressedImage->encode('webp')->save(public_path('ProductGallery') . '/' . $filename . '.webp');
                
                $gallery->image = $filename . '.webp';
            
                $gallery->image = $filename;
            
                $gallery->save();
            }
            
        }

        if ($request->varients != null) {
            foreach ($request->varients as $varientData) {
                $varient = ProductVarient::where('id',$varientData['id'])->first();
        
                if ($varient) {
                    $varient->color = $varientData['color'];
                    $varient->size = $varientData['size'];
                    $varient->bolt_pattern = $varientData['bolt_pattern'];
                    $varient->price = $varientData['varient_price'];
                    $varient->discount_price = $varientData['varient_discount_price'];
                    $varient->sku = $varientData['varient_sku'];
                    $varient->stock = $varientData['varient_stock'];
                    // if($request->file('varient_image'))
                    // {
                    //     $file= $varientData['varient_image'];
                    //     $filename= date('YmdHis').$file->getClientOriginalName();
                    //     $file->move(public_path('ProductVarient'),$filename);

                    //     $compressedImage = Image::make(public_path('ProductVarient') . '/' . $filename)
                    //     ->encode('jpg', 50); 
                
                        
                    //     $compressedFilename = 'compressed_' . $filename;
                    //     $compressedImage->save(public_path('ProductVarient') . '/' . $compressedFilename);
                
                    //     unlink(public_path('ProductVarient/'.$filename));

                    //     $varient->image = $compressedFilename;
                    // }
                    $varient->save();
                } else {
                    $varient = new ProductVarient();
                    $varient->product_id = $update->id;
                    $varient->color = $varientData['color'];
                    $varient->size = $varientData['size'];
                    $varient->bolt_pattern = $varientData['bolt_pattern'];
                    $varient->price = $varientData['varient_price'];
                    $varient->discount_price = $varientData['varient_discount_price'];
                    $varient->sku = $varientData['varient_sku'];
                    $varient->stock = $varientData['varient_stock'];
                    // if($request->file('varient_image'))
                    // {
                    //     $file= $varientData['varient_image'];
                    //     $filename= date('YmdHis').$file->getClientOriginalName();
                    //     $file->move(public_path('ProductVarient'),$filename);

                    //     $compressedImage = Image::make(public_path('ProductVarient') . '/' . $filename)
                    //     ->encode('jpg', 50); 
                
                        
                    //     $compressedFilename = 'compressed_' . $filename;
                    //     $compressedImage->save(public_path('ProductVarient') . '/' . $compressedFilename);
                
                    //     unlink(public_path('ProductVarient/'.$filename));

                    //     $varient->image = $compressedFilename;
                    // }
                    $varient->save();
                }
            }
        }

        if($request->discount != null)
        {
            $discount = Discount::where('product_id',$update->id)->first();
            $discount->product_id = $update->id;
            $discount->discount = $request->discount;
            $discount->discount_start_date = $request->discount_start_date;
            $discount->discount_end_date = $request->discount_end_date;
            $discount->discount_type = $request->discount_type;
            $discount->save();
        }

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
            $tax =  Tax::where('product_id',$update->id)->first();
            $tax->product_id = $update->id;
            $tax->tax = $request->tax;
            $tax->tax_type = $request->tax_type;
            $tax->save();
        }


        if($request->deal_id != null)
        {
            $deal = DealProduct::where('product_id',$update->id)->first();
            $deal->deal_id = $request->deal_id;
            $deal->product_id = $update->id;
            $deal->discount = $request->deal_discount;
            $deal->discount_type = $request->deal_discount_type;
            $deal->save();
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


        $response = ['status'=>true,"message" => "Product updated Successfully!"];
        return response($response, 200);
    }

    public function delete($id)
    {
        $file = Product::find($id);

        $gallery = ProductGallery::where('product_id',$id)->get();
        foreach($gallery as $item)
        {
            if($item->image)
            {
                unlink(public_path('ProductGallery/'.$item->image));
            }
        }
        
        $varients = ProductVarient::where('product_id',$id)->get();
        foreach($varients as $item)
        {
            if($item->image)
            {
                unlink(public_path('ProductVarient/'.$item->image));
            }
        }


      if($file->meta_img)
      {
          unlink(public_path('ProductMetaImg/'.$file->meta_img));
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
                if($item1->image)
                {
                    unlink(public_path('ProductGallery/'.$item1->image));
                }
            }
            
            $varients = ProductVarient::where('product_id',$item->id)->get();
            foreach($varients as $item2)
            {
                if($item2->image)
                {
                    unlink(public_path('ProductVarient/'.$item2->image));
                }
            }
    
    
          if($item->meta_img)
          {
              unlink(public_path('ProductMetaImg/'.$item->meta_img));
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
