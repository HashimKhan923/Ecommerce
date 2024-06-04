<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'shipping_cost',
        'is_qty_multiply',
        'shipping_additional_cost',
        'est_shipping_days',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
