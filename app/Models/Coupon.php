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

    use HasFactory;
}
