<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $casts = [
        'photos' => 'array',
        'colors' => 'array',
        'sizes' => 'array',
        'tags' => 'array',
    ];
    use HasFactory;
}
