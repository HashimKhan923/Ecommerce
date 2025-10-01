<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

        protected $fillable = [
        'customer_id', 'seller_id', 'total_amount',
        'status', 'discount_amount', 'discount_given_at'
    ];
    public function customer()
    {
        return $this->belongsTo(User::class,'customer_id','id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class,'seller_id','id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id','id');
    }

    public function varient()
    {
        return $this->belongsTo(ProductVarient::class,'varient_id','id');
    }

    public function shipping()
    {
        return $this->belongsTo(Shipping::class,'shipping_id','id');
    }
}
