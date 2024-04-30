<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;


    public function seller()
    {
        return $this->belongsTo(User::class,'seller_id','id');
    } 

    public function customer()
    {
        return $this->belongsTo(User::class,'customer_id','id');
    } 

    public function shop()
    {
        return $this->belongsTo(Shop::class,'shop_id','id');
    } 

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id','id');
    } 
}
