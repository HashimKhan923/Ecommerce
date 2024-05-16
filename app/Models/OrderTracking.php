<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTracking extends Model
{
    protected $fillable = ['order_id','tracking_number','courier_name','courier_link','shipping_label'];

    use HasFactory;
}
