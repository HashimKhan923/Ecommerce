<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $casts = [
        'photos' => 'array',
        'colors' => 'array',
        'sizes' => 'array',
        'tags' => 'array',
    ];

    public function ratings()
    {
        return $this->hasMany(ProductRating::class);
    }

    public function getRatingAttribute()
    {
        return $this->ratings()->avg('rating');
    }

    
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    } 

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id','id');
    } 

    public function brand()
    {
        return $this->belongsTo(Brand::class,'brand_id','id');
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
        return $this->belongsTo(Deal::class,'deal_id','id');
    } 

    


    use HasFactory;
}
