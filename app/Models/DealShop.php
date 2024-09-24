<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealShop extends Model
{
    use HasFactory;

    protected $fillable = ['deal_id','shop_id'];
}
