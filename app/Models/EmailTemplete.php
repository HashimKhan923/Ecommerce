<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplete extends Model
{
    use HasFactory;

    protected $fillable = ['seller_id','name','content'];
}
