<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockNotifyMe extends Model
{
    use HasFactory;

    protected $fillable = ['email','product_id','seller_id','variant_id','status'];


    public function product()
    {
        return $this->belongsTo(Product::class,'product_id','id');
    }

    public function varient()
    {
        return $this->belongsTo(ProductVarient::class,'varient_id','id');
    }
}
