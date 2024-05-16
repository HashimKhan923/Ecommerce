<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedProductOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'product_id',
        'seller_id',
        'product_price',
        'quantity',
        'payment',
        'payment_status'
    ];


    public function product() {
        return $this->belongsTo(Product::class,'product_id','id');
    }
}
