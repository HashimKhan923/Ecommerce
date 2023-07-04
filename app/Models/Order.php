<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{   
    protected $casts = [
        'information' => 'array',

    ];

    public function order_detail()
    {
        return $this->hasMany(OrderDetail::class,'order_id','id');
    } 

    use HasFactory;
}
