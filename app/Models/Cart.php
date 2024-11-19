<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

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
