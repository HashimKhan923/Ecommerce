<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    use HasFactory;

    protected $fillable = ['date','seller_id','shop_id','order_id','status'];

    public function order() {

        return $this->belongsTo(Order::class,'order_id','id');
    }

    public function seller() {

        return $this->belongsTo(User::class,'seller_id','id');
    }

    public function listing_fee()
    {
        return $this->belongsTo(ProductListingPayment::class, 'product_listing_id','id');
    }

    public function featuredProductOrders()
    {
        return $this->hasMany(FeaturedProductOrder::class, 'order_id', 'order_id');
    }

    
}
