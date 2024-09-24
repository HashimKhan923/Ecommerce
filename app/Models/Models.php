<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Models extends Model
{
    protected $casts = [
        'meta_keywords' => 'array',
    ];

    protected $fillable = ['order'];
    use HasFactory;
}
