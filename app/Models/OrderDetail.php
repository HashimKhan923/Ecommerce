<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_image',
        'product_varient',
        'product_price',
        'shipping_amount',
        'quantity',
        'varient_id'
    ];

    protected $casts = [
        'varient' => 'array',
    ];

    public function products() {
        return $this->belongsTo(Product::class,'product_id','id');
    }

    public function varient() {
        return $this->belongsTo(ProductVarient::class,'varient_id','id');
    }

    public function order() {
        return $this->belongsTo(Order::class,'order_id','id');
    }
}
