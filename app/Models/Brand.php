<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $casts = [
        'meta_keywords' => 'array',
    ];

    protected $fillable = ['order'];

    public function model()
    {
        return $this->hasMany(Models::class,'brand_id','id');
    }

    public function product()
    {
        return $this->hasMany(Product::class,'brand_id','id');
    }
}
