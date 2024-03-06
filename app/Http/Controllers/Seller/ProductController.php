<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Discount;
use App\Models\Shipping;
use App\Models\Stock;
use App\Models\Tax;
use App\Models\WholesaleProduct;
use App\Models\SubscribeUser;
use App\Models\ProductVarient;
use App\Models\DealProduct;
use App\Models\ProductGallery;
use App\Models\Color;
use App\Models\ProductListingPayment;
use App\Models\User;
use Carbon\Carbon;
use Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;

class ProductController extends Controller
{
    public function index($id)
    {
       $Products = Product::with([
    'user',
    'category',
    'brand',
    'stock',
    'product_gallery' => function($query) {
        $query->orderBy('order', 'asc');
    },
    'discount',
    'tax',
    'shipping',
    'deal.deal_product',
    'wholesale',
    'shop.shop_policy',
    'reviews',
    'product_varient'
])->where('user_id', $id)->get();


        return response()->json(['Products'=>$Products]);
    }

    public function create(Request $request)
    {

        $new = new Product();
        $new->name = $request->name;
        $new->added_by = 'seller';
        $new->user_id = $request->user_id;
        $new->category_id = $request->category_id;
        $new->height = $request->height;
        $new->weight = $request->weight;
        $new->lenght = $request->lenght;
        $new->start_year = $request->year;
        $new->make = $request->make;
        $new->unit = $request->unit;
        $new->sku = $request->sku;
        $new->bar_code = $request->bar_code;
        $new->condition = $request->condition;
        $new->brand_id = $request->brand_id;
        $new->model_id = $request->model_id;
        $new->shop_id = $request->shop_id;
        $new->tags = $request->tags;
        $new->trim = $request->trim;
        $new->description = $request->description;
        $new->price = $request->price;
        $new->cost_price = $request->cost_price;
        $new->shipping = $request->shipping;
        $new->featured = $request->featured;
        $new->published = $request->published;
        $new->is_tax = $request->is_tax;
        $new->meta_title = $request->meta_title;
        $new->meta_discription = $request->meta_description;
        $new->video = $request->video;
        $new->slug = $request->slug;
        $new->save();

        $order = 0;

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $image) {
                $gallery = new ProductGallery();
                $gallery->product_id = $new->id;
                $gallery->order = $order++;
    
                $filename = date('YmdHis') . $image->getClientOriginalName();
    
                $compressedImage = Image::make($image->getRealPath());
    
                $compressedImage->encode('webp')->save(public_path('ProductGallery') . '/' . $filename . '.webp');
    
                $gallery->image = $filename . '.webp';
    
                $gallery->save();
            }
        }
    
        elseif ($request->filled('photos')) {
            $photoNames = $request->photos;
            foreach ($photoNames as $photoName) {
                $gallery = new ProductGallery();
                $gallery->product_id = $new->id;
                $gallery->order = $order++;
                $gallery->image = $photoName;
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
                if ($item['varient_image']) 
                {
                    $image = $item['varient_image'];

                    $filename = date('YmdHis'). '_' . uniqid() . '.' .$image->getClientOriginalName();
    
                    $compressedImage = Image::make($image->getRealPath());
        
                    $compressedImage->encode('webp')->save(public_path('ProductVarient') . '/' . $filename . '.webp');
        
                    $varient->image  = $filename . '.webp';
                    
                }
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



        

        if($request->shipping_type != null)
        {
            $shipping = new Shipping();
            $shipping->product_id = $new->id;
            $shipping->shipping_cost = $request->shipping_cost;
            $shipping->is_qty_multiply = $request->is_qty_multiply;
            $shipping->shipping_additional_cost = $request->shipping_additional_cost;
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


        $sellerT = User::where('id',$request->user_id)->first();
        $userRegistrationDate = $sellerT->created_at;

        if ($userRegistrationDate->diffInMonths(now()) > 3) {
            $SellerCheck = ProductListingPayment::where('seller_id',$request->user_id)->where('payment_status','unpaid')->first();
            if(!$SellerCheck)
            {
                $newListing = new ProductListingPayment();
                $newListing->seller_id = $request->user_id;
                $newListing->listing_count = 1;
                $newListing->listing_amount = 0.20;
                $newListing->save();
            }
            else
            {
                $SellerCheck->listing_count = $SellerCheck->listing_count + 1;
                $SellerCheck->listing_amount = $SellerCheck->listing_amount + 0.20;
                $SellerCheck->save();
            }

        } 





        $response = ['status'=>true,"message" => "Product Added Successfully!",'product_id'=>$new->id];
        return response($response, 200);
        

    }




    public function update(Request $request)
    {
        return $request->varients;

        $update = Product::where('id',$request->id)->first();
        $update->name = $request->name;
        $update->added_by = 'seller';
        $update->user_id = $request->user_id;
        $update->category_id = $request->category_id;
        $update->height = $request->height;
        $update->weight = $request->weight;
        $update->lenght = $request->lenght;
        $update->start_year = $request->year;
        $update->make = $request->make;
        $update->unit = $request->unit;
        $update->sku = $request->sku;
        $update->condition = $request->condition;
        $update->brand_id = $request->brand_id;
        $update->model_id = $request->model_id;
        $update->tags = $request->tags;
        $update->trim = $request->trim;
        $update->description = $request->description;
        $update->price = $request->price;
        $update->cost_price = $request->cost_price;
        $update->shop_id = $request->shop_id;
        $update->shipping = $request->shipping;
        $update->featured = $request->featured;
        $update->published = $request->published;
        $update->is_tax = $request->is_tax;
        $update->meta_title = $request->meta_title;
        $update->video = $request->video;
        $update->slug = $request->slug;
        $update->sku = $request->sku;
        $update->save();


        if ($request->photos) {
            $images = $request->photos;
        
            foreach ($images as $image) {

                if (isset($image['image_id']))
                {
                    $gallery = ProductGallery::where('id', $image['image_id'])->first();
                    $gallery->order = $image['order'];
                }
                else
                {
                    $gallery = new ProductGallery();
                    $gallery->product_id = $update->id;
                    $gallery->order = $image['order'];
        
                    $filename = date('YmdHis') . $image['file']->getClientOriginalName();
                    $compressedImage = Image::make($image['file']->getRealPath());
                    $compressedImage->encode('webp')->save(public_path('ProductGallery') . '/' . $filename . '.webp');
                    $gallery->image = $filename . '.webp';
                }

        
                $gallery->save();


            
        }

        if ($request->varients) {
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
                    if($varientData['varient_image'])
                    {

                        $checkCount = ProductVarient::where('image',$varient->image)->count();

                        if($checkCount < 2)
                        {
                            unlink(public_path('ProductVarient/'.$varient->image));
                        }

                    $image = $varientData['varient_image'];

                    $filename = date('YmdHis') . $image->getClientOriginalName();
    
                    $compressedImage = Image::make($image->getRealPath());
        
                    $compressedImage->encode('webp')->save(public_path('ProductVarient') . '/' . $filename . '.webp');
        
                    $varient->image  = $filename . '.webp';

                    }
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
                    if($varientData['varient_image'])
                    {
                        $image = $varientData['varient_image'];

                        $filename = date('YmdHis') . $image->getClientOriginalName();
        
                        $compressedImage = Image::make($image->getRealPath());
            
                        $compressedImage->encode('webp')->save(public_path('ProductVarient') . '/' . $filename . '.webp');
            
                        $varient->image  = $filename . '.webp';

                    }
                    $varient->save();
                }
            }
        }

        if($request->discount != null)
        {
            $discount = Discount::where('product_id',$update->id)->first();

            if($discount == null)
            {
                $discount = new Discount();
            }

            $discount->product_id = $update->id;
            $discount->discount = $request->discount;
            $discount->discount_start_date = $request->discount_start_date;
            $discount->discount_end_date = $request->discount_end_date;
            $discount->discount_type = $request->discount_type;
            $discount->save();

        }
        else
        {
            $check_discount = Discount::where('product_id',$update->id)->first();

            if($check_discount)
            {
                $check_discount->delete();
            }
        }

        if($request->stock != null)
        {
            $stock = Stock::where('product_id',$update->id)->first();

            if($stock == null)
            {
                $stock = new Stock();
            }
 
            $stock->product_id = $update->id;
            $stock->stock = $request->stock;
            $stock->min_stock = $request->min_stock;
            $stock->save();

        }

        
        // if($request->tax != null)
        // {
        //     $tax =  Tax::where('product_id',$update->id)->first();

        //     if($tax == null)
        //     {
        //         $tax = new Tax();
        //     }


        //         $tax->product_id = $update->id;
        //         $tax->tax = $request->tax;
        //         $tax->tax_type = $request->tax_type;
        //         $tax->save();

        // }



        

        if($request->shipping_type)
        {

            $shipping = Shipping::where('product_id',$update->id)->first();

            if($shipping == null)
            {
                $shipping = new Shipping();
            }


                $shipping->product_id = $update->id;
                $shipping->shipping_cost = $request->shipping_cost;
                $shipping->is_qty_multiply = $request->is_qty_multiply;
                $shipping->shipping_additional_cost = $request->shipping_additional_cost;
                $shipping->est_shipping_days = $request->est_shipping_days;
                $shipping->save();
            

        }

        if($request->wholesale_price != null)
        {
            WholesaleProduct::where('product_id',$update->id)->delete();

            foreach($request->wholesale_price as $price)
            {
                $wholesale = new WholesaleProduct();
                $wholesale->product_id = $update->id;
                $wholesale->wholesale_price = $price;
                $wholesale->wholesale_min_qty = $request->wholesale_min_qty;
                $wholesale->wholesale_max_qty = $request->wholesale_max_qty;
                $wholesale->save();               
            }
        }

        $response = ['status'=>true,"message" => "Product updated Successfully!"];
        return response($response, 200);
    }

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
                unlink(public_path('ProductGallery/'.$item->image));
            }
        }
        
        $varients = ProductVarient::where('product_id',$id)->get();
        foreach($varients as $item)
        {
            $checkCount = ProductVarient::where('image',$item->image)->count();

            if($checkCount < 2)
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
                
                $checkCount = ProductGallery::where('image',$item1->image)->count();

                if($checkCount < 2)
                {
                    unlink(public_path('ProductGallery/'.$item1->image));
                }
            }
            
            $varients = ProductVarient::where('product_id',$item->id)->get();
            foreach($varients as $item2)
            {
                $checkCount = ProductVarient::where('image',$item2->image)->count();

                if($checkCount < 2)
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

    public function is_multiple_published(Request $request)
    {

        Product::whereIn('id', $request->ids)->update(['published' => 1]);

        $response = ['status'=>true,"message" => "Status Changed Successfully!"];
        return response($response, 200);

    }


    public function is_multiple_draft(Request $request)
    {

        Product::whereIn('id', $request->ids)->update(['published' => 0]);
    
        $response = ['status'=>true,"message" => "Status Changed Successfully!"];
        return response($response, 200);

    }

    public function view($id)
    {
        $data = Product::with('user','category','brand','stock','product_gallery','discount','tax','shipping','deal.deal_product','wholesale','shop','reviews','product_varient'
        )->where('id',$id)->get();

        return response()->json(['data'=>$data]);
    }

    public function is_featured(Request $request)
    {

        // $checkPackage = SubscribeUser::where('user_id',$request->user_id)->first();

        // if($checkPackage && $checkPackage->product_upload_limit > 0)
        // {


        $is_featured = Product::where('id',$request->product_id)->first();
        if($is_featured->featured == 1)
        {
            $is_featured->featured = 0;
        }
        else
        {
            $is_featured->featured = 1;
        }
        $is_featured->save();

        // $dedect = SubscribeUser::where('user_id',$request->user_id)->first();
        // $dedect->product_upload_limit = $dedect->product_upload_limit - 1;
        // $dedect->save();

        $response = ['status'=>true,"message" => "Status Changed Successfully!"];
        return response($response, 200);

        // }
        // else
        // {
        //     $response = ['status'=>true,"message" => "you dont have any subscription to feature products. please buy any subscription"];
        //     return response($response, 401);
        // }
    }

    public function gallery_delete($id)
    {
      $file = ProductGallery::find($id);

        $checkCount = ProductGallery::where('image',$file->image)->count();

        if($checkCount < 2)
        {
            unlink(public_path('ProductGallery/'.$file->image));
        }
  
        $file->delete();

        $response = ['status'=>true,"message" => "Deleted Successfully!"];
        return response($response, 200);
    }
}
