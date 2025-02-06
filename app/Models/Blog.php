<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $casts = [
        'meta_keywords' => 'array',
        'tags'=>'array'
    ];


    public function blog_category()
    {
        return $this->belongsTo(BlogCategory::class,'blogcat_id','id');
    } 

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    } 
}
