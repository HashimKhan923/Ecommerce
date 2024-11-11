<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockNotifyMe extends Model
{
    use HasFactory;

    protected $fillable = ['email','product_id','variant_id','status'];
}
