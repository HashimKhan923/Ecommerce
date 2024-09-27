<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    use HasFactory;

    public function products()
    {
        return $this->hasMany(Product::class,'deal_id','id');
    } 

    public function deal_shop()
    {
        return $this->hasMany(DealShop::class,'deal_id','id');
    } 
}
