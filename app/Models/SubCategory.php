<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;
    protected $fillable = ['order'];

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id','id');
    } 

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_sub_category', 'sub_category_id', 'category_id');
    }

    public function product()
    {
        return $this->hasMany(Product::class,'sub_category_id','id');
    }


}
