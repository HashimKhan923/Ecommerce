<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderForcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'month',
        'predicted_orders',
        'predicted_revenue',
        'insight'
    ];

}
