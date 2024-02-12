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
    'shop',
    'reviews',
    'product_varient'
])->where('user_id', $id)->get();


        return response()->json(['Products'=>$Products]);
    }

    public function create(Request $request)
    {



        
        // $checkPackage = SubscribeUser::where('user_id',$request->user_id)->first();
        // if($checkPackage)
        // {
        $new = new Product();
        $new->name = $request->name;
        $new->added_by = 'seller';
        $new->user_id = $request->user_id;
        $new->category_id = $request->category_id;
        $new->height = $request->height;
        $new->weight = $request->weight;
        $new->lenght = $request->lenght;
        $new->start_year = $request->year;
        // $new->end_year = $request->end_year;
        $new->make = $request->make;
        $new->unit = $request->unit;
        $new->sku = $request->sku;
        $new->bar_code = $request->bar_code;
        $new->condition = $request->condition;
        $new->brand_id = $request->brand_id;
        $new->model_id = $request->model_id;
        $new->shop_id = $request->shop_id;
        $new->deal_id = $request->deal_id;
        $new->tags = $request->tags;
        $new->description = $request->description;
        $new->price = $request->price;
        $new->shipping = $request->shipping;
        $new->featured = $request->featured;
        $new->published = $request->published;
        $new->is_tax = $request->is_tax;
        $new->meta_title = $request->meta_title;
        $new->meta_description = $request->meta_description;
        // if($request->file('meta_img'))
        // {
        //     $file= $request->meta_img;
        //     $filename= date('YmdHis').$file->getClientOriginalName();
        //     $file->move(public_path('ProductMetaImg'),$filename);

        //     $compressedImage = Image::make(public_path('ProductMetaImg') . '/' . $filename)
        //     ->encode('webp', 70); 
    
            
        //     $compressedFilename = 'compressed_' . $filename;
        //     $compressedImage->save(public_path('ProductMetaImg') . '/' . $compressedFilename);
    
        //     unlink(public_path('ProductMetaImg/'.$filename));


        //     $new->meta_img = $compressedFilename;
        // }
        $new->slug = $request->slug;
        $new->save();

        $order = 0;

        if ($request->photos) {
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
                //         ->encode('webp', 70); 
                
                        
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

        
        // if($request->tax != null)
        // {
        //     $tax = new Tax();
        //     $tax->product_id = $new->id;
        //     $tax->tax = $request->tax;
        //     $tax->tax_type = $request->tax_type;
        //     $tax->save();
        // }


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

        // $dedect = SubscribeUser::where('user_id',auth()->user()->id)->first();
        // $dedect->product_upload_limit = $dedect->product_upload_limit - 1;
        // $dedect->save();



        $response = ['status'=>true,"message" => "Product Added Successfully!"];
        return response($response, 200);

        // }
        // else
        // {
        //     $response = ['status'=>true,"message" => "you dont have any subscription to upload new product. please buy any subscription to upload products!"];
        //     return response($response, 401);
        // }
        
        

    }

    public function sell_similar($id)
    {
        $existingProduct = Product::find($id);

        if ($existingProduct) {
            $new = new Product();
            $new->name = $existingProduct->name;
            $new->added_by = 'seller';
            $new->user_id = $existingProduct->user_id;
            $new->category_id = $existingProduct->category_id;
            $new->height = $existingProduct->height;
            $new->weight = $existingProduct->weight;
            $new->lenght = $existingProduct->lenght;
            $new->start_year = $existingProduct->year;
            $new->make = $existingProduct->make;
            $new->unit = $existingProduct->unit;
            $new->sku = $existingProduct->sku;
            $new->bar_code = $existingProduct->bar_code;
            $new->condition = $existingProduct->condition;
            $new->brand_id = $existingProduct->brand_id;
            $new->model_id = $existingProduct->model_id;
            $new->shop_id = $existingProduct->shop_id;
            $new->deal_id = $existingProduct->deal_id;
            $new->tags = $existingProduct->tags;
            $new->description = $existingProduct->description;
            $new->price = $existingProduct->price;
            $new->shipping = $existingProduct->shipping;
            $new->featured = $existingProduct->featured;
            $new->published = $existingProduct->published;
            $new->is_tax = $existingProduct->is_tax;
            $new->meta_title = $existingProduct->meta_title;
            $new->meta_description = $existingProduct->meta_description;
            $new->slug = $existingProduct->slug;
            $new->save();
        }

        $ProductGallery = ProductGallery::where('product_id',$id)->get();

        if ($ProductGallery) {
            foreach($ProductGallery as $item)
            {
                $gallery = new ProductGallery();
                $gallery->product_id = $new->id;
                $gallery->order = $item->order;
                $gallery->image = $item->image;
                $gallery->save();
            }
        } 

        $ProductVarient = ProductVarient::where('product_id',$id)->get();

        if ($ProductVarient) {
            foreach($ProductVarient as $item)
            {
                $varient = new ProductVarient();
                $varient->product_id = $new->id;
                $varient->color = $item->color;
                $varient->size = $item->size;
                $varient->bolt_pattern = $item->bolt_pattern;
                $varient->price = $item->varient_price;
                $varient->discount_price = $item->varient_discount_price;
                $varient->sku = $item->varient_sku;
                $varient->stock = $item->varient_stock;
                $varient->save();
            }
        } 

        $Discount = Discount::where('product_id',$id)->first();

        if ($Discount) {

            $discount = new Discount();
            $discount->product_id = $new->id;
            $discount->discount = $Discount->discount;
            $discount->discount_start_date = $Discount->discount_start_date;
            $discount->discount_end_date = $Discount->discount_end_date;
            $discount->discount_type = $Discount->discount_type;
            $discount->save();
        } 

        $Stock = Stock::where('product_id',$id)->first();

        if ($Stock) {

            $stock = new Stock();
            $stock->product_id = $new->id;
            $stock->stock = $Stock->stock;
            $stock->min_stock = $Stock->min_stock;
            $stock->save();
        } 

        $DealProduct = DealProduct::where('product_id',$id)->first();

        if ($DealProduct) {

            $deal = new DealProduct();
            $deal->deal_id = $DealProduct->deal_id;
            $deal->product_id = $new->id;
            $deal->discount = $DealProduct->deal_discount;
            $deal->discount_type = $DealProduct->deal_discount_type;
            $deal->save();
        } 


        $Shipping = Shipping::where('product_id',$id)->first();

        if ($Shipping) {

            $shipping = new Shipping();
            $shipping->product_id = $new->id;
            $shipping->shipping_cost = $Shipping->shipping_cost;
            $shipping->is_qty_multiply = $Shipping->is_qty_multiply;
            $shipping->est_shipping_days = $Shipping->est_shipping_days;
            $shipping->save();
        } 
    }


    public function update(Request $request)
    {
        $update = Product::where('id',$request->id)->first();
        $update->name = $request->name;
        $update->added_by = 'seller';
        $update->user_id = $request->user_id;
        $update->category_id = $request->category_id;
        $update->height = $request->height;
        $update->weight = $request->weight;
        $update->lenght = $request->lenght;
        $update->start_year = $request->year;
        // $update->end_year = $request->end_year;
        $update->make = $request->make;
        $update->unit = $request->unit;
        $update->sku = $request->sku;
        $update->bar_code = $request->bar_code;
        $update->condition = $request->condition;
        $update->brand_id = $request->brand_id;
        $update->model_id = $request->model_id;
        $update->deal_id = $request->deal_id;
        $update->tags = $request->tags;
        $update->description = $request->description;
        $update->price = $request->price;
        $update->shop_id = $request->shop_id;
        $update->shipping = $request->shipping;
        $update->featured = $request->featured;
        $update->published = $request->published;
        $update->is_tax = $request->is_tax;
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
        //     ->encode('webp', 70); 
    
            
        //     $compressedFilename = 'compressed_' . $filename;
        //     $compressedImage->save(public_path('ProductMetaImg') . '/' . $compressedFilename);
    
        //     unlink(public_path('ProductMetaImg/'.$filename));


        //     $update->meta_img = $filename;
        // }
        $update->slug = $request->slug;
        $update->sku = $request->sku;
        $update->save();

        // $request->merge(['photos' => $photoArray]);

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
                    //     ->encode('webp', 70); 
                
                        
                    //     $compressedFilename = 'compressed_' . $filename;
                    //     $compressedImage->save(public_path('ProductVarient') . '/' . $compressedFilename);
                
                    //     unlink(public_path('ProductVarient/'.$filename));

                    //     $varient->image = $filename;
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
                    //     ->encode('webp', 70); 
                
                        
                    //     $compressedFilename = 'compressed_' . $filename;
                    //     $compressedImage->save(public_path('ProductVarient') . '/' . $compressedFilename);
                
                    //     unlink(public_path('ProductVarient/'.$filename));


                    //     $varient->image = $filename;
                    // }
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


        if($request->deal_id != null)
        {
            $deal = DealProduct::where('product_id',$update->id)->first();

            if($deal == null)
            {
                $deal = new DealProduct();
            }

                $deal->deal_id = $request->deal_id;
                $deal->product_id = $update->id;
                $deal->discount = $request->deal_discount;
                $deal->discount_type = $request->deal_discount_type;
                $deal->save();
            

        }
        

        if($request->shipping_type != null)
        {
            $shipping = Shipping::where('product_id',$update->id)->first();

            if($shipping == null)
            {
                $shipping = new Shipping();
            }


                $shipping->product_id = $update->id;
                $shipping->shipping_cost = $request->shipping_cost;
                $shipping->is_qty_multiply = $request->is_qty_multiply;
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
