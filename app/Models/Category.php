<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $casts = [
        'meta_keywords' => 'array',
    ];

    protected $fillable = ['order'];

    public function sub_category()
    {
        return $this->hasMany(SubCategory::class,'category_id','id');
    }

    public function product()
    {
        return $this->hasMany(Product::class,'category_id','id');
    }
}
