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

    public function shop_product($shop_id)
    {
        $products = Product::with('product_single_gallery')->select('id','name')->where('shop_id',$shop_id)->get();
        return response()->json(['Products' => $products]);
    }

    public function index($user_id, $shop_id = null, $status = null, $isFeatured = null)
    {
        $products = $this->loadMoreProducts($user_id, $shop_id, 0, 50, $status, $isFeatured);
        return response()->json(['Products' => $products]);
    }

    
    private function loadMoreProducts($userId, $start, $length, $shopId, $status, $isFeatured, $searchValue, $dealId)
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
        ])->where('user_id', $userId);
    

        if ($shopId != 0) {
            $query->where('shop_id', $shopId);
        }

        if ($searchValue != 0) {
            $query->where(function($subQuery) use ($searchValue) {
                $subQuery->where('id', $searchValue)
                         ->orWhere('name', 'LIKE', "%$searchValue%")
                         ->orWhere('sku', $searchValue);
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
    
    public function load_more($userId, $start, $length, $shopId, $status, $isFeatured, $searchValue)
    {
        
        
        if ($start < 0) {
            $start = 0;
        }
    
        $products = $this->loadMoreProducts($userId, $start, $length, $shopId, $status, $isFeatured, $searchValue);
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
    

    public function create(Request $request)
    {

        // $validator = Validator::make($request->all(), [
        //     "sku" => "unique:products,sku",
        // ]);
        // if ($validator->fails())
        // {
        //     return response(['errors'=>$validator->errors()->all()], 422);
        // }

    $new = Product::create([
        'name' => $request->name,
        'added_by' => 'seller',
        'user_id' => $request->user_id,
        'category_id' => $request->category_id,
        'sub_category_id' => $request->sub_category_id,
        'height' => $request->height,
        'weight' => $request->weight,
        'width' => $request->width,
        'lenght' => $request->lenght,
        'start_year' => $request->year,
        'make' => $request->make,
        'unit' => $request->unit,
        'bar_code' => $request->bar_code,
        'warranty' => $request->warranty,
        'condition' => $request->condition,
        'brand_id' => $request->brand_id,
        'model_id' => $request->model_id,
        'shop_id' => $request->shop_id,
        'tags' => $request->tags,
        'trim' => $request->trim,
        'description' => $request->description,
        'price' => $request->price,
        'cost_price' => $request->cost_price,
        'shipping' => $request->shipping,
        'featured' => $request->featured,
        'published' => $request->published,
        'is_tax' => $request->is_tax,
        'meta_title' => $request->meta_title,
        'meta_discription' => $request->meta_description,
        'video' => $request->video,
        'slug' => $request->slug
    ]);

    $new->update([
        'sku' => $request->sku . '-' . $new->id
    ]);

    if($request->string_images)
    {
        foreach($request->string_images as $image)
        {
            ProductGallery::create([
                'product_id' => $new->id,
                'image' => $image['file'],
                'order' => $image['order']
            ]);
        }
    }

    if ($request->photos) {
       
        foreach ($request->photos as $image) {
                // Generate a unique filename
                $filename = date('YmdHis') . '_' . (string) Str::uuid() . '.webp';
                
                // Compress and save the image
                $compressedImage =ImageFacade::make($image['file']->getRealPath());
                $compressedImage->encode('webp')->save(public_path('ProductGallery') . '/' . $filename . '.webp');
                
                // Set the filename with the new extension
                $filename = $filename . '.webp';

            // Create a new ProductGallery entry
            ProductGallery::create([
                'product_id' => $new->id,
                'image' => $filename,
                'order' => $image['order'],
            ]);
        }
    }


        
    if ($request->varients != null) {
        foreach ($request->varients as $item) {
            $imagePath = null;

            if (is_uploaded_file($item['varient_image'])) {
                $image = $item['varient_image'];
                $filename = date('YmdHis') . $image->getClientOriginalName();
                $compressedImage = ImageFacade::make($image->getRealPath());
                $compressedImage->encode('webp')->save(public_path('ProductVarient') . '/' . $filename . '.webp');
                $imagePath = $filename . '.webp';
            } else {
                $imagePath = $item['varient_image'];
            }

            ProductVarient::create([
                'product_id' => $new->id,
                'color' => $item['color'],
                'size' => $item['size'],
                'bolt_pattern' => $item['bolt_pattern'],
                'others' => $item['others'],
                'price' => $item['varient_price'],
                'discount_price' => $item['varient_discount_price'],
                'sku' => $item['varient_sku'],
                'stock' => $item['varient_stock'],
                'image' => $imagePath
            ]);
        }
    }

    if ($request->discount != null) {
        Discount::create([
            'product_id' => $new->id,
            'discount' => $request->discount,
            'discount_start_date' => $request->discount_start_date,
            'discount_end_date' => $request->discount_end_date,
            'discount_type' => $request->discount_type,
        ]);
    }
    
    if ($request->stock != null) {
        Stock::create([
            'product_id' => $new->id,
            'stock' => $request->stock,
            'min_stock' => $request->min_stock,
        ]);
    }
    
    if ($request->deal_id != null) {
        DealProduct::create([
            'deal_id' => $request->deal_id,
            'product_id' => $new->id,
            'discount' => $request->deal_discount,
            'discount_type' => $request->deal_discount_type,
        ]);
    }
    
    if ($request->shipping_type != null) {
        Shipping::create([
            'product_id' => $new->id,
            'shipping_cost' => $request->shipping_cost,
            'is_qty_multiply' => $request->is_qty_multiply,
            'shipping_additional_cost' => $request->shipping_additional_cost,
            'est_shipping_days' => $request->est_shipping_days,
        ]);
    }




        // $sellerT = User::where('id',$request->user_id)->first();
        // $userRegistrationDate = $sellerT->created_at;

        // if ($sellerT->created_at < Carbon::now()->subMonths(3)) {

        //     $SellerCheck = ProductListingPayment::where('seller_id',$request->user_id)->where('payment_status','unpaid')->first();
        //     if(!$SellerCheck)
        //     {
        //         $newListing = new ProductListingPayment();
        //         $newListing->seller_id = $request->user_id;
        //         $newListing->listing_count = 1;
        //         $newListing->listing_amount = 0.20;
        //         $newListing->save();
        //     }
        //     else
        //     {
        //         $SellerCheck->listing_count = $SellerCheck->listing_count + 1;
        //         $SellerCheck->listing_amount = $SellerCheck->listing_amount + 0.20;
        //         $SellerCheck->save();
        //     }

        // } 

        $response = ['status'=>true,"message" => "Product Added Successfully!",'product_id'=>$new->id];
        return response($response, 200);



    }



    public function bulk_create(Request $request)
    {
       
        if (is_array($request->products)) {
            $responses = [];
    
            foreach ($request->products as $productData) {
            
                $new = Product::create([
                    'name' => $productData['name'] ?? null,
                    'added_by' => 'seller',
                    'user_id' => $productData['user_id'],
                    'category_id' => $productData['category_id'] ?? null,
                    'sub_category_id' => $productData['sub_category_id'] ?? null,
                    'height' => $productData['height'] ?? 0,
                    'weight' => $productData['weight'] ?? 0,
                    'lenght' => $productData['lenght'] ?? 0,
                    'width' => $productData['width'] ?? 0,
                    'start_year' => $productData['year'] ?? null,
                    'make' => $productData['make'] ?? null,
                    'unit' => $productData['unit'] ?? null,
                    'bar_code' => $productData['bar_code'] ?? null,
                    'condition' => $productData['condition'] ?? null,
                    'brand_id' => $productData['brand_id'] ?? null,
                    'model_id' => $productData['model_id'] ?? null,
                    'shop_id' => $productData['shop_id'] ?? null,
                    'tags' => $productData['tags'] ?? '',
                    'trim' => $productData['trim'] ?? '',
                    'description' => $productData['description'] ?? null,
                    'price' => $productData['price'] ?? 0.0,
                    'cost_price' => $productData['cost_price'] ?? 0.0,
                    'shipping' => $productData['shipping'] ?? 0.0,
                    'featured' => $productData['featured'] ?? false,
                    'published' => $productData['published'] ?? false,
                    'is_tax' => $productData['is_tax'] ?? false,
                    'meta_title' => $productData['meta_title'] ?? null,
                    'meta_discription' => $productData['meta_description'] ?? null,
                    'video' => $productData['video'] ?? null,
                    'slug' => $productData['slug'] ?? null
                ]);

                $new->update([
                    'sku' => $productData['sku'] . '-' . $new->id ?? null,
                ]);
    
                try {
                    // Handle photo uploads
                    if (isset($productData['photos'])) {
                        $order = 1;
                        foreach ($productData['photos'] as $url) {
                            // $response = Http::get($url);
    
                            // if ($response->successful()) {
                            //     $imageContent = $response->body();
                            //     $image = ImageFacade::make($imageContent);
                            //     $filename = date('YmdHis') . '_' . (string) Str::uuid() . '.webp';
    
                            //     if (!File::exists(public_path('ProductGallery'))) {
                            //         File::makeDirectory(public_path('ProductGallery'), 0755, true);
                            //     }
    
                            //     $image->encode('webp')->save(public_path('ProductGallery') . '/' . $filename);
    
                                ProductGallery::create([
                                    'product_id' => $new->id,
                                    'order' => $order++,
                                    'image' => $url
                                ]);
                            // } else {
                            //     return response()->json(['message' => 'Failed to download one or more images'], 500);
                            // }
                        }
                    }
    
                    // Handle variants
                    if (!empty($productData['varients'])) {
                        foreach ($productData['varients'] as $item) {
                            // $imagePath = null;
    
                            // if (isset($item['varient_image'])) {
                            //     $response = Http::get($item['varient_image']);
    
                            //     if ($response->successful()) {
                            //         $imageContent = $response->body();
                            //         $image = ImageFacade::make($imageContent);
                            //         $filename = date('YmdHis') . '_' . (string) Str::uuid() . '.webp';
    
                            //         if (!File::exists(public_path('ProductVarient'))) {
                            //             File::makeDirectory(public_path('ProductVarient'), 0755, true);
                            //         }
    
                            //         $image->encode('webp')->save(public_path('ProductVarient') . '/' . $filename);
                            //         $imagePath = $filename;
                            //     } else {
                            //         return response()->json(['message' => 'Failed to download one or more images'], 500);
                            //     }
                            // }
    
                            ProductVarient::create([
                                'product_id' => $new->id,
                                'color' => $item['color'] ?? null,
                                'size' => $item['size'] ?? null,
                                'bolt_pattern' => $item['bolt_pattern'] ?? null,
                                'others' => $item['others'] ?? null,
                                'price' => $item['varient_price'] ?? 0.0,
                                'discount_price' => $item['varient_discount_price'] ?? 0.0,
                                'sku' => $item['varient_sku'] ?? null,
                                'stock' => $item['varient_stock'] ?? 0,
                                'image' => $item['varient_image'] ?? null
                            ]);
                        }
                    }
    
                    // Handle discount
                    if (!empty($productData['discount'])) {
                        Discount::create([
                            'product_id' => $new->id,
                            'discount' => $productData['discount'] ?? 0.0,
                            'discount_start_date' => $productData['discount_start_date'] ?? null,
                            'discount_end_date' => $productData['discount_end_date'] ?? null,
                            'discount_type' => $productData['discount_type'] ?? null
                        ]);
                    }
    
                    // Handle stock
                    if (!empty($productData['stock']) || $productData['stock'] == 0) {
                        Stock::create([
                            'product_id' => $new->id,
                            'stock' => $productData['stock'] ?? 0,
                            'min_stock' => $productData['min_stock'] ?? 0
                        ]);
                    }
    
                    // Handle deals
                    if (!empty($productData['deal_id'])) {
                        DealProduct::create([
                            'deal_id' => $productData['deal_id'] ?? 0,
                            'product_id' => $new->id,
                            'discount' => $productData['deal_discount'] ?? 0.0,
                            'discount_type' => $productData['deal_discount_type'] ?? null
                        ]);
                    }
    
                    // Handle shipping
                    if (!empty($productData['shipping_type'])) {
                        Shipping::create([
                            'product_id' => $new->id,
                            'shipping_cost' => $productData['shipping_cost'] ?? 0.0,
                            'is_qty_multiply' => $productData['is_qty_multiply'] ?? false,
                            'shipping_additional_cost' => $productData['shipping_additional_cost'] ?? 0.0,
                            'est_shipping_days' => $productData['est_shipping_days'] ?? 0
                        ]);
                    }
    
                    // Handle wholesale prices
                    if (!empty($productData['wholesale_price'])) {
                        foreach ($productData['wholesale_price'] as $price) {
                            WholesaleProduct::create([
                                'product_id' => $new->id,
                                'wholesale_price' => $price ?? 0.0,
                                'wholesale_min_qty' => $productData['wholesale_min_qty'] ?? 0,
                                'wholesale_max_qty' => $productData['wholesale_max_qty'] ?? 0
                            ]);
                        }
                    }
    
                    $responses[] = ['status' => true, "message" => "Product Added Successfully!", 'product_id' => $new->id];
                } catch (\Exception $e) {
                    return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
                }
            }
    
            return response(['status' => true, 'messages' => $responses], 200);
        }
    
        return response(['status' => false, "message" => "Invalid request format!"], 400);
    }
    




    public function update(Request $request)
    {

        // $validator = Validator::make($request->all(), [
        //     "sku" => "unique:products,sku," . $request->id,
        // ]);
        
        // if ($validator->fails()) {
        //     return response(['errors' => $validator->errors()->all()], 422);
        // }

        $product = Product::find($request->id);
        
        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found!'], 404);
        }
    
        $productData = $request->only([
            'name', 'user_id', 'category_id', 'sub_category_id', 'height', 'weight',
            'lenght','width','year', 'make', 'unit', 'sku', 'bar_code', 'condition', 
            'brand_id', 'model_id', 'tags', 'trim','warranty','description', 'price', 
            'cost_price', 'shop_id', 'shipping', 'featured', 'published', 'is_tax',
            'meta_title','meta_description','video', 'slug'
        ]);
        
        $productData['added_by'] = 'seller';
        if(isset($productData['year']))
        {
            $productData['start_year'] = $productData['year'];
            unset($productData['year']);
        }



    
        $product->update($productData);
    
        try {

            if($request->string_images)
            {
                foreach($request->string_images as $image)
                {
                    if (isset($image['image_id'])) {
                        $gallery = ProductGallery::find($image['image_id']);
                        if ($gallery) {
                            $gallery->update(['order' => $image['order']]);
                        }
                    } else {
                    ProductGallery::create([
                        'product_id' => $product->id,
                        'image' => $image['file'],
                        'order' => $image['order']
                    ]);
                    }
                }
            }


            // Update product images
            if ($request->photos) {
                foreach ($request->photos as $image) {
                    if (isset($image['image_id'])) {
                        $gallery = ProductGallery::find($image['image_id']);
                        if ($gallery) {
                            $gallery->update(['order' => $image['order']]);
                        }
                    } else {
                        $filename = date('YmdHis') . '_' . (string) Str::uuid() . '.webp';
                        $compressedImage = ImageFacade::make($image['file']->getRealPath());
                        $compressedImage->encode('webp')->save(public_path('ProductGallery') . '/' . $filename);
    
                        ProductGallery::create([
                            'product_id' => $product->id,
                            'order' => $image['order'],
                            'image' => $filename
                        ]);
                    }
                }
            }
    
            // Update product variants
            if ($request->varients) {
                foreach ($request->varients as $varientData) {
                    $varient = ProductVarient::find($varientData['id']);
                    $varientDataFormatted = [
                        'color' => $varientData['color'],
                        'size' => $varientData['size'],
                        'bolt_pattern' => $varientData['bolt_pattern'],
                        'others' => $varientData['others'],
                        'price' => $varientData['varient_price'],
                        'discount_price' => $varientData['varient_discount_price'],
                        'sku' => $varientData['varient_sku'],
                        'stock' => $varientData['varient_stock']
                    ];
    
                    if ($varient) {
                        if (isset($varientData['varient_image']) && is_uploaded_file($varientData['varient_image'])) {
                            $checkCount = ProductVarient::where('image', $varient->image)->count();

                                if ($checkCount < 2) {
                                    $fileToDelete = public_path('ProductVarient/'.$varient->image);

                                    if (file_exists($fileToDelete)) {
                                        unlink($fileToDelete);
                                    } 
                                }
                            

    
                            $image = $varientData['varient_image'];
                            $filename = date('YmdHis') . '_' . (string) Str::uuid() . '.webp';
                            $compressedImage = ImageFacade::make($image->getRealPath());
                            $compressedImage->encode('webp')->save(public_path('ProductVarient') . '/' . $filename);
                            $varientDataFormatted['image'] = $filename;
                        }
                        else
                        {
                            $varientDataFormatted['image'] = $varientData['varient_image'];
                        }
                        $varient->update($varientDataFormatted);
                    } else {
                        if (isset($varientData['varient_image']) && is_uploaded_file($varientData['varient_image'])) {
                            $image = $varientData['varient_image'];
                            $filename = date('YmdHis') . '_' . (string) Str::uuid() . '.webp';
                            $compressedImage = ImageFacade::make($image->getRealPath());
                            $compressedImage->encode('webp')->save(public_path('ProductVarient') . '/' . $filename);
                            $varientDataFormatted['image'] = $filename;
                        }
                        else
                        {
                            $varientDataFormatted['image'] = $varientData['varient_image'];
                        }
                        $varientDataFormatted['product_id'] = $product->id;
                        ProductVarient::create($varientDataFormatted);
                    }
                }
            }
    
            // Update discount
            if ($request->has('discount')) {
                Discount::updateOrCreate(
                    ['product_id' => $product->id],
                    [
                        'discount' => $request->discount,
                        'discount_start_date' => $request->discount_start_date,
                        'discount_end_date' => $request->discount_end_date,
                        'discount_type' => $request->discount_type
                    ]
                );
            } else {
                Discount::where('product_id', $product->id)->delete();
            }
    
            // Update stock
            if ($request->has('stock')) {
                Stock::updateOrCreate(
                    ['product_id' => $product->id],
                    [
                        'stock' => $request->stock,
                        'min_stock' => $request->min_stock
                    ]
                );
            }
    
            // Update deal
            if ($request->has('deal_id')) {
                DealProduct::updateOrCreate(
                    ['product_id' => $product->id],
                    [
                        'deal_id' => $request->deal_id,
                        'discount' => $request->deal_discount,
                        'discount_type' => $request->deal_discount_type
                    ]
                );
            }
    
            // Update shipping
            if ($request->has('shipping_type')) {
                Shipping::updateOrCreate(
                    ['product_id' => $product->id],
                    [
                        'shipping_cost' => $request->shipping_cost,
                        'is_qty_multiply' => $request->is_qty_multiply,
                        'shipping_additional_cost' => $request->shipping_additional_cost,
                        'est_shipping_days' => $request->est_shipping_days
                    ]
                );
            }
    
            // Update wholesale prices
            if ($request->has('wholesale_price')) {
                WholesaleProduct::where('product_id', $product->id)->delete();
                foreach ($request->wholesale_price as $price) {
                    WholesaleProduct::create([
                        'product_id' => $product->id,
                        'wholesale_price' => $price,
                        'wholesale_min_qty' => $request->wholesale_min_qty,
                        'wholesale_max_qty' => $request->wholesale_max_qty
                    ]);
                }
            }
    
            return response()->json(['status' => true, "message" => "Product updated Successfully!"], 200);
    
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }


    }



    

    public function bulk_update(Request $request)
    {

       

        foreach ($request->products as $productData) {

            $validator = Validator::make($productData, [
                "sku" => [
                    "unique:products,sku," . $productData['id'],
                ],
            ], [
                'sku.unique' => "The SKU :input is already taken for product ID {$productData['id']}."
            ]);
        
            if ($validator->fails()) {
                return response(['errors'=>$validator->errors()->all()], 422);
            }

            $product = Product::find($productData['id']);
            
            if ($product) {
                $productFields = array_filter($productData, function($key) {
                    return in_array($key, [
                        'name', 'category_id', 'sub_category_id','sku','height', 'weight',
                        'lenght','width','make', 'brand_id', 'model_id', 'tags', 'price', 'shop_id'
                    ]);
                }, ARRAY_FILTER_USE_KEY);
                
                $product->update($productFields);

                // Update product variants
                if (isset($productData['product_variants'])) {
                    foreach ($productData['product_variants'] as $variantData) {
                        $variant = ProductVarient::find($variantData['id']);
                        
                        if ($variant) {
                            $variantFields = array_filter($variantData, function($key) {
                                return in_array($key, ['varient_price', 'varient_discount_price', 'varient_stock']);
                            }, ARRAY_FILTER_USE_KEY);

                            if (isset($variantFields['varient_price'])) {
                                $variantFields['price'] = $variantFields['varient_price'];
                                unset($variantFields['varient_price']);
                            }

                            if (isset($variantFields['varient_discount_price'])) {
                                $variantFields['discount_price'] = $variantFields['varient_discount_price'];
                                unset($variantFields['varient_discount_price']);
                            }

                            if (isset($variantFields['varient_sku'])) {
                                $variantFields['sku'] = $variantFields['varient_sku'];
                                unset($variantFields['varient_sku']);
                            }

                            if (isset($variantFields['varient_stock'])) {
                                $variantFields['stock'] = $variantFields['varient_stock'];
                                unset($variantFields['varient_stock']);
                            }

                            $variant->update($variantFields);
                        }
                    }
                }

                // Update stock
                $stock = Stock::where('product_id', $productData['id'])->firstOrNew(['product_id' => $productData['id']]);
                if (isset($productData['stock']) || $productData['stock'] === 0) {
                    $stock->stock = $productData['stock'];
                }
                $stock->save();

                // Update discount
                if (!empty($productData['discount'])) {
                    Discount::updateOrCreate(
                        ['product_id' => $product->id],
                        ['discount' => $productData['discount']]
                    );
                }
                else
                {
                    Discount::where('product_id', $product->id)->delete();
                    
                }

                // Update shipping
                if (isset($productData['shipping_cost']) || $productData['shipping_cost'] === 0) {
                    $shippingData = array_filter($productData, function($key) {
                        return in_array($key, ['shipping_cost', 'shipping_additional_cost', 'est_shipping_days']);
                    }, ARRAY_FILTER_USE_KEY);

                    Shipping::updateOrCreate(
                        ['product_id' => $product->id],
                        $shippingData
                    );
                }
            }
        }


            return response()->json(['status' => true, "message" => "Products updated successfully!"], 200);
    }


    public function updated_data(Request $request)
    {
        $Products = Product::with([
            'user', 'category.sub_category', 'brand.model', 'model', 'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            },'discount', 'tax', 'shipping', 'deal.deal_product',
            'wholesale', 'shop.shop_policy', 'reviews.user', 'product_varient'
        ])->whereIn('id',$request->ids)->get();

        return response()->json(['data'=>$Products]);
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
