<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdGallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'ad_id',
        'image_path',
    ];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    
}
