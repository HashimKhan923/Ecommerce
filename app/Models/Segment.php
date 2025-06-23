<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{

    protected $fillable = ['seller_id','name', 'rules'];
    protected $casts = ['rules' => 'array'];

    public function seller()
    {
        return $this->belongsTo(User::class,'seller_id','id');
    } 

    use HasFactory;
}
