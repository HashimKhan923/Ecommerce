<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
        protected $fillable = [
        'product_id',
        'discount',
        'discount_start_date',
        'discount_end_date',
        'discount_type',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    use HasFactory;
}
