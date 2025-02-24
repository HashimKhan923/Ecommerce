<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVarient extends Model
{
    use HasFactory;

    protected $fillable = [
        'custom_variant_id','product_id', 'color', 'size', 'bolt_pattern', 'others', 
        'price', 'discount_price', 'sku', 'stock', 'image','url'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
