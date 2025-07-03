<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{   
    protected $casts = [
        'information' => 'array',
        'tags'=>'array',
        'tax'=>'array',
        'signature'=>'array',
        'insurance'=>'array'
    ];

    protected $fillable = [
        'order_code',
        'number_of_products',
        'customer_id',
        'shop_id',
        'sellers_id',
        'amount',
        'information',
        'stripe_payment_id',
        'payment_method',
        'payment_status',
        'refund',
        'signature',
        'insurance',
        'tax',
        'view_status'
    ];

    public function order_detail()
    {
        return $this->hasMany(OrderDetail::class,'order_id','id');
    } 

    public function order_status()
    {
        return $this->hasMany(OrderStatus::class,'order_id','id');
    } 

    public function nagative_payout_balance()
    {
        return $this->hasOne(NagativePayoutBalance::class,'order_id','id');
    } 

    public function order_tracking()
    {
        return $this->hasOne(OrderTracking::class,'order_id','id');
    } 

    public function order_refund()
    {
        return $this->hasOne(Refund::class,'order_id','id');
    } 

    public function seller()
    {
        return $this->belongsTo(User::class,'sellers_id','id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class,'customer_id','id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class,'shop_id','id');
    } 

    public function coupon_user()
    {
        return $this->hasMany(CouponUser::class,'order_id','id');
    } 

    public function order_timeline()
    {
        return $this->hasMany(OrderTimeline::class,'order_id','id');
    } 

    public function payout()
    {
        return $this->hasOne(Payout::class,'order_id','id');
    } 

    use HasFactory;
}
