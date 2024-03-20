<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $casts = [
        'shop_id' => 'array',
        'product_id' => 'array',
        'customer_id' => 'array',
        'category_id' => 'array',
        'brand_id' => 'array',
        'model_id' => 'array',
    ];

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
