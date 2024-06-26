<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponUser extends Model
{
    use HasFactory;

    protected $fillable = ['coupon_id','user_id','discount','coupon_code','order_id'];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class,'coupon_id','id');
    }
}
