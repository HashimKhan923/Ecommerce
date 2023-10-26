<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscribeUser extends Model
{

    protected $casts = [
        'end_time' => 'datetime',
    ];
    use HasFactory;
}
