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
use File;
use Intervention\Image\Facades\Image as ImageFacade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;


class ProductController extends Controller
{
    public function index($id)
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
        $new->video = $request->video;
        $new->slug = $request->slug;
        $new->save();

        if ($request->photos) {
            foreach ($request->file('photos') as $image) {
                $gallery = new ProductGallery();
                $gallery->product_id = $new->id;   

                $filename = date('YmdHis') . $image->getClientOriginalName();

                $compressedImage = ImageFacade::make($image->getRealPath());
                
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
                $varient->others = $item['others'];
                $varient->price = $item['varient_price'];
                $varient->discount_price = $item['varient_discount_price'];
                $varient->sku = $item['varient_sku'];
                $varient->stock = $item['varient_stock'];
                if(is_uploaded_file($item['varient_image']))
                {
                    $image = $item['varient_image'];

                    $filename = date('YmdHis') . $image->getClientOriginalName();
    
                    $compressedImage = ImageFacade::make($image->getRealPath());
        
                    $compressedImage->encode('webp')->save(public_path('ProductVarient') . '/' . $filename . '.webp');
        
                    $varient->image = $filename . '.webp';
                }
                else
                {
                    $varient->image = $item['varient_image'];
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

        if ($sellerT->created_at < Carbon::now()->subMonths(3)) {

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



    public function bulk_create(Request $request)
    {
        if (is_array($request->products)) {
            $responses = [];

    
            foreach ($request->products as $productData) {
            
                $new = new Product();
                $new->name = $productData['name'] ?? null;
                $new->added_by = 'seller';
                $new->user_id = $productData['user_id'];
                $new->category_id = $productData['category_id'] ?? null;
                $new->height = $productData['height'] ?? 0;
                $new->weight = $productData['weight'] ?? 0;
                $new->lenght = $productData['lenght'] ?? 0;
                $new->start_year = $productData['year'] ?? date('Y');
                $new->make = $productData['make'] ?? null;
                $new->unit = $productData['unit'] ?? null;
                $new->sku = $productData['sku'] ?? null;
                $new->bar_code = $productData['bar_code'] ?? null;
                $new->condition = $productData['condition'] ?? null;
                $new->brand_id = $productData['brand_id'] ?? null;
                $new->model_id = $productData['model_id'] ?? null;
                $new->shop_id = $productData['shop_id'] ?? null;
                $new->tags = $productData['tags'] ?? '';
                $new->trim = $productData['trim'] ?? '';
                $new->description = $productData['description'] ?? null;
                $new->price = $productData['price'] ?? 0.0;
                $new->cost_price = $productData['cost_price'] ?? 0.0;
                $new->shipping = $productData['shipping'] ?? 0.0;
                $new->featured = $productData['featured'] ?? false;
                $new->published = $productData['published'] ?? false;
                $new->is_tax = $productData['is_tax'] ?? false;
                $new->meta_title = $productData['meta_title'] ?? null;
                $new->video = $productData['video'] ?? null;
                $new->slug = $productData['slug'] ?? null;
                $new->save();


        $imageNames = [];

        try {
            if(isset($productData['photos']))
            {
                foreach ($productData['photos'] as $url) {
                    $order = 1;
                    // Download the image content
                    $response = Http::get($url);
    
                    // Ensure the request was successful
                    if ($response->successful()) {
                        // Get the image content
                        $imageContent = $response->body();
    
                        // Create an Intervention Image instance from the downloaded content
                        $image = ImageFacade::make($imageContent);
    
                        // Generate a unique filename with a UUID and the current timestamp
                        $filename = date('YmdHis') . '_' . (string) Str::uuid() . '.webp';
    
                        // Ensure the ProductGallery directory exists
                        if (!File::exists(public_path('ProductGallery'))) {
                            File::makeDirectory(public_path('ProductGallery'), 0755, true);
                        }
    
                        // Save the image in WebP format to the specified path
                        $image->encode('webp')->save(public_path('ProductGallery') . '/' . $filename);
    
                        // Save the image name to the database
                                    $gallery = new ProductGallery();
                                    $gallery->product_id = $new->id;
                                    $gallery->order = $order++;
                                    $gallery->image = $filename;
                                    $gallery->save();
    
                        // Store the image filename
                        $imageNames[] = $filename;
                    } else {
                        return response()->json(['message' => 'Failed to download one or more images'], 500);
                    }
                }
            }



        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    
    
                
    
                if (!empty($productData['varients'])) {
                    foreach ($productData['varients'] as $item) {
                        $varient = new ProductVarient();
                        $varient->product_id = $new->id;
                        $varient->color = $item['color'] ?? null;
                        $varient->size = $item['size'] ?? null;
                        $varient->bolt_pattern = $item['bolt_pattern'] ?? null;
                        $varient->others = $item['others'] ?? null;
                        $varient->price = $item['varient_price'] ?? 0.0;
                        $varient->discount_price = $item['varient_discount_price'] ?? 0.0;
                        $varient->sku = $item['varient_sku'] ?? null;
                        $varient->stock = $item['varient_stock'] ?? 0;
                            if(isset($item['varient_image']))
                            {
                                // Download the image content
                            $response = Http::get($item['varient_image']);

                            // Ensure the request was successful
                            if ($response->successful()) {
                                // Get the image content
                                $imageContent = $response->body();

                                // Create an Intervention Image instance from the downloaded content
                                $image = ImageFacade::make($imageContent);

                                // Generate a unique filename with a UUID and the current timestamp
                                $filename = date('YmdHis') . '_' . (string) Str::uuid() . '.webp';

                                // Ensure the ProductGallery directory exists
                                if (!File::exists(public_path('ProductVarient'))) {
                                    File::makeDirectory(public_path('ProductVarient'), 0755, true);
                                }

                                // Save the image in WebP format to the specified path
                                $image->encode('webp')->save(public_path('ProductVarient') . '/' . $filename);
                                $varient->image = $filename;
                        } else {
                            return response()->json(['message' => 'Failed to download one or more images'], 500);
                        }
                            }
                        
                        $varient->save();
                    }
                }
    
                if (!empty($productData['discount'])) {
                    $discount = new Discount();
                    $discount->product_id = $new->id;
                    $discount->discount = $productData['discount'] ?? 0.0;
                    $discount->discount_start_date = $productData['discount_start_date'] ?? null;
                    $discount->discount_end_date = $productData['discount_end_date'] ?? null;
                    $discount->discount_type = $productData['discount_type'] ?? null;
                    $discount->save();
                }
    
                if (!empty($productData['stock']) || $productData['stock'] == 0) {
                    $stock = new Stock();
                    $stock->product_id = $new->id;
                    $stock->stock = $productData['stock'] ?? 0;
                    $stock->min_stock = $productData['min_stock'] ?? 0;
                    $stock->save();
                }
    
                if (!empty($productData['deal_id'])) {
                    $deal = new DealProduct();
                    $deal->deal_id = $productData['deal_id'] ?? 0;
                    $deal->product_id = $new->id;
                    $deal->discount = $productData['deal_discount'] ?? 0.0;
                    $deal->discount_type = $productData['deal_discount_type'] ?? null;
                    $deal->save();
                }
    
                if (!empty($productData['shipping_type'])) {
                    $shipping = new Shipping();
                    $shipping->product_id = $new->id;
                    $shipping->shipping_cost = $productData['shipping_cost'] ?? 0.0;
                    $shipping->is_qty_multiply = $productData['is_qty_multiply'] ?? false;
                    $shipping->shipping_additional_cost = $productData['shipping_additional_cost'] ?? 0.0;
                    $shipping->est_shipping_days = $productData['est_shipping_days'] ?? 0;
                    $shipping->save();
                }
    
                if (!empty($productData['wholesale_price'])) {
                    foreach ($productData['wholesale_price'] as $price) {
                        $wholesale = new WholesaleProduct();
                        $wholesale->product_id = $new->id;
                        $wholesale->wholesale_price = $price ?? 0.0;
                        $wholesale->wholesale_min_qty = $productData['wholesale_min_qty'] ?? 0;
                        $wholesale->wholesale_max_qty = $productData['wholesale_max_qty'] ?? 0;
                        $wholesale->save();
                    }
                }
    
                $sellerT = User::where('id', $productData['user_id'])->first();
                $userRegistrationDate = $sellerT->created_at;
    
                if ($sellerT->created_at < Carbon::now()->subMonths(3)) {
                    $SellerCheck = ProductListingPayment::where('seller_id', $productData['user_id'])->where('payment_status', 'unpaid')->first();
                    if (!$SellerCheck) {
                        $newListing = new ProductListingPayment();
                        $newListing->seller_id = $productData['user_id'];
                        $newListing->listing_count = 1;
                        $newListing->listing_amount = 0.20;
                        $newListing->save();
                    } else {
                        $SellerCheck->listing_count += 1;
                        $SellerCheck->listing_amount += 0.20;
                        $SellerCheck->save();
                    }
                }
    
                $responses[] = ['status' => true, "message" => "Product Added Successfully!", 'product_id' => $new->id];
            }
    
            return response(['status' => true, 'messages' => $responses], 200);
        }
    
        return response(['status' => false, "message" => "Invalid request format!"], 400);
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
        $update->make = $request->make;
        $update->unit = $request->unit;
        $update->sku = $request->sku;
        $update->bar_code = $request->bar_code;
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
                    $compressedImage = ImageFacade::make($image['file']->getRealPath());
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
                    $varient->others = $varientData['others'];
                    $varient->price = $varientData['varient_price'];
                    $varient->discount_price = $varientData['varient_discount_price'];
                    $varient->sku = $varientData['varient_sku'];
                    $varient->stock = $varientData['varient_stock'];
                    
                    if(is_uploaded_file($varientData['varient_image']))
                    {

                        $checkCount = ProductVarient::where('image',$varient->image)->count();

                        if($checkCount < 2)
                        {
                            unlink(public_path('ProductVarient/'.$varient->image));
                        }

                        $image = $varientData['varient_image'];
    
                        $filename = date('YmdHis') . $image->getClientOriginalName();
        
                        $compressedImage = ImageFacade::make($image->getRealPath());
            
                        $compressedImage->encode('webp')->save(public_path('ProductVarient') . '/' . $filename . '.webp');
            
                        $varient->image = $filename . '.webp';
                    }
                    $varient->save();
                } else {
                    $varient = new ProductVarient();
                    $varient->product_id = $update->id;
                    $varient->color = $varientData['color'];
                    $varient->size = $varientData['size'];
                    $varient->bolt_pattern = $varientData['bolt_pattern'];
                    $varient->others = $varientData['others'];
                    $varient->price = $varientData['varient_price'];
                    $varient->discount_price = $varientData['varient_discount_price'];
                    $varient->sku = $varientData['varient_sku'];
                    $varient->stock = $varientData['varient_stock'];
                    if(is_uploaded_file($varientData['varient_image']))
                    {
                        $image = $varientData['varient_image'];
    
                        $filename = date('YmdHis') . $image->getClientOriginalName();
        
                        $compressedImage = ImageFacade::make($image->getRealPath());
            
                        $compressedImage->encode('webp')->save(public_path('ProductVarient') . '/' . $filename . '.webp');
            
                        $varient->image = $filename . '.webp';
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

    public function bulk_update(Request $request)
    {

        foreach ($request->products as $productData) {
            $update = Product::find($productData['id']);
    
            if ($update) {
                $update->name = $productData['name'];
                if (isset($productData['category_id'])) {
                    $update->category_id = $productData['category_id'];
                }
                if (isset($productData['weight'])) {
                    $update->weight = $productData['weight'];
                }
                if (isset($productData['make'])) {
                    $update->make = $productData['make'];
                }
                if (isset($productData['brand_id'])) {
                    $update->brand_id = $productData['brand_id'];
                }
                if (isset($productData['model_id'])) {
                    $update->model_id = $productData['model_id'];
                }
                if (isset($productData['tags'])) {
                    $update->tags = $productData['tags'];
                }
                if (isset($productData['price'])) {
                    $update->price = $productData['price'];
                }
                if (isset($productData['shop_id'])) {
                    $update->shop_id = $productData['shop_id'];
                }
                $update->save();
    
                if (!empty($productData['product_variants'])) {
                    foreach ($productData['product_variants'] as $variantData) {
                        $variant = ProductVarient::find($variantData['id']);
    
                        if ($variant) {
                            $variant->price = $variantData['varient_price'];
                            if (isset($productData['varient_discount_price'])) {
                            $variant->discount_price = $variantData['varient_discount_price'];
                            }
                            $variant->stock = $variantData['varient_stock'];
                            $variant->save();
                        }
                    }
                }
    
                $stock = Stock::where('product_id', $update->id)->firstOrNew(['product_id' => $update->id]);
                if (isset($productData['stock']) || $productData['stock'] == 0) {
                    $stock->stock = $productData['stock'];
                }
                if (isset($productData['min_stock']) || $productData['stock'] == 0) {
                    $stock->min_stock = $productData['min_stock'];
                }
                $stock->save();

                if(!empty($productData['discount']))
                {
                    $discount = Discount::where('product_id', $update->id)->firstOrNew(['product_id' => $update->id]);
                    $discount->product_id = $update->id;
                    $discount->discount = $productData['discount'];
                    $discount->save();
        
                }
                // else
                // {
                //     $check_discount = Discount::where('product_id',$update->id)->first();
        
                //     if($check_discount)
                //     {
                //         $check_discount->delete();
                //     }
                // }
    
                if (!empty($productData['shipping_cost']) || $productData['shipping_cost'] == 0) {
                    $shipping = Shipping::where('product_id', $update->id)->firstOrNew(['product_id' => $update->id]);
                    $shipping->shipping_cost = $productData['shipping_cost'];
                    if (isset($productData['shipping_additional_cost']) || $productData['shipping_additional_cost'] == 0 ) {
                    $shipping->shipping_additional_cost = $productData['shipping_additional_cost'];
                    }
                    if (isset($productData['est_shipping_days'])) {
                    $shipping->est_shipping_days = $productData['est_shipping_days'];
                    }
                    $shipping->save();
                }
        }
        
    }



        $Products = Product::with([
            'user', 'category', 'brand', 'model', 'stock',
            'product_gallery','discount', 'tax', 'shipping', 'deal.deal_product',
            'wholesale', 'shop.shop_policy', 'reviews.user', 'product_varient'
        ])->where('id',$request->ids)->get();


        $response = ['status'=>true,"message" => "Products updated Successfully!",'Products'=>$Products];
        return response($response, 200);
    }

    

    public function delete($id)
    {
        $file = Product::find($id);

        $gallery = ProductGallery::where('product_id',$id)->get();
        foreach($gallery as $item)
        {
            $checkCount = ProductGallery::where('image',$item->image)->count();


                unlink(public_path('ProductGallery/'.$item->image));
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
                    $fileToDelete = public_path('ProductGallery/'.$item1->image);

                        if (file_exists($fileToDelete)) {
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

                    if (file_exists($fileToDelete)) {
                        unlink($fileToDelete);
                    } 
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


    public function is_multiple_featured(Request $request)
    {

        Product::whereIn('id', $request->ids)->update(['featured' => 1]);

        $response = ['status'=>true,"message" => "Status Changed Successfully!"];
        return response($response, 200);

    }


    public function is_multiple_unfeatured(Request $request)
    {

        Product::whereIn('id', $request->ids)->update(['featured' => 0]);
    
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

        $response = ['status'=>true,"message" => "Status Changed Successfully!"];
        return response($response, 200);

    }

    public function gallery_delete($id)
    {
        
      $file = ProductGallery::find($id);

        $checkCount = ProductGallery::where('image',$file->image)->count();

        if($checkCount < 2)
        {
            $fileToDelete = public_path('ProductGallery/'.$file->image);

            if (file_exists($fileToDelete)) {
                unlink($fileToDelete);
            } 
        }
  
        $file->delete();

        $response = ['status'=>true,"message" => "Deleted Successfully!"];
        return response($response, 200);
    }
}
