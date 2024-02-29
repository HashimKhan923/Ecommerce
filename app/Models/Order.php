<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{   
    protected $casts = [
        'information' => 'array',
        'tags'=>'array'
    ];

    public function order_detail()
    {
        return $this->hasMany(OrderDetail::class,'order_id','id');
    } 

    public function order_status()
    {
        return $this->hasMany(OrderStatus::class,'order_id','id');
    } 

    public function order_tracking()
    {
        return $this->hasOne(OrderTracking::class,'order_id','id');
    } 

    public function order_refund()
    {
        return $this->hasOne(Refund::class,'order_id','id');
    } 

    use HasFactory;
}
