<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $casts = [
        'photos' => 'array',
        'color_id' => 'array',
        'size_id' => 'array',
        'tags' => 'array',
    ];
    use HasFactory;
}
