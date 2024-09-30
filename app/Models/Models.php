<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Models extends Model
{
    protected $casts = [
        'meta_keywords' => 'array',
    ];

    public function product()
    {
        return $this->hasMany(Product::class,'category_id','id');
    }

    protected $fillable = ['order'];
    use HasFactory;
}
