<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    public function seller()
    {
        return $this->belongsTo(User::class,'sellers_id');
    }

    public function product()
    {
        return $this->hasMany(Product::class,'shop_id');
    }

    public function order()
    {
        return $this->hasMany(Order::class,'shop_id');
    }

    // public function payout()
    // {
    //     return $this->hasMany(Payout::class,'seller_id','seller_id');
    // }
}
