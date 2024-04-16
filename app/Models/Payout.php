<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    use HasFactory;

    public function order() {

        return $this->belongsTo(Order::class,'order_id','id');
    }

    public function listing_fee()
    {
        return $this->belongsTo(ProductListingPayment::class, 'seller_id', 'seller_id')->where('payment_status', 'paid');
    }
    
}
