<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Product extends Model
{



    protected $casts = [
        'start_year' => 'array',
        'tags' => 'array',
        'trim' => 'array',
        'warranty' => 'array'
    ];



    protected $fillable = [
        'custom_id','name','description','added_by', 'user_id', 'category_id', 'sub_category_id', 'height', 
        'weight','width','lenght', 'start_year', 'make', 'unit', 'sku', 'bar_code', 
        'condition', 'brand_id', 'model_id', 'shop_id','warranty','tags', 'trim', 
        'description', 'price', 'cost_price','average_rating','shipping', 'featured', 
        'published', 'is_tax', 'meta_title','meta_description','video', 'slug'
    ];




    
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    } 

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id','id');
    } 

    public function sub_category()
    {
        return $this->belongsTo(SubCategory::class,'sub_category_id','id');
    } 

    public function brand()
    {
        return $this->belongsTo(Brand::class,'brand_id','id');
    } 

    public function shop()
    {
        return $this->belongsTo(Shop::class,'shop_id','id');
    } 

    public function model()
    {
        return $this->belongsTo(Models::class,'model_id','id');
    } 

    public function product_varient()
    {
        return $this->hasMany(ProductVarient::class,'product_id','id');
    }

    public function product_gallery()
    {
        return $this->hasMany(ProductGallery::class,'product_id','id');
    }

    public function product_single_gallery()
    {
        return $this->hasOne(ProductGallery::class,'product_id','id');
    }

    public function wishlistProduct()
    {
        if (auth()->check()) {
            return $this->hasOne(Whishlist::class, 'product_id', 'id')
                        ->where('customer_id', auth()->id());
        }
    
        return $this->hasOne(Whishlist::class, 'product_id', 'id')->whereRaw('1 = 0');
    
    }

    public function stock()
    {
        return $this->hasOne(Stock::class,'product_id','id');
    }

    public function discount()
    {
        return $this->hasOne(Discount::class,'product_id','id');
    }

    public function tax()
    {
        return $this->hasOne(Tax::class,'product_id','id');
    }

    public function shipping()
    {
        return $this->hasOne(Shipping::class,'product_id','id');
    }

    public function wholesale()
    {
        return $this->hasMany(WholesaleProduct::class,'product_id','id');
    }

    public function wishlist()
    {
        return $this->hasMany(Whishlist::class,'product_id','id');
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    } 

    public function reviews() {
        return $this->hasMany(Review::class);
    }

    public function updateAverageRating()
    {
        $averageRating = $this->reviews()->avg('rating');
        $this->update(['average_rating' => $averageRating]);
    }

    


    use HasFactory;
}