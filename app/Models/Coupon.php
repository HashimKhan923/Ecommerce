<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
 

    public function shop()
    {
        return $this->belongsTo(Shop::class,'shop_id','id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class,'creator_id','id');
    }

    public function coupon_customers()
    {
        return $this->hasMany(CouponCustomer::class,'coupon_id');
    }

    public function coupon_categories()
    {
        return $this->hasMany(CouponCategory::class,'coupon_id');
    }

    public function coupon_products()
    {
        return $this->hasMany(CouponProduct::class,'coupon_id');
    }

    use HasFactory;
}
