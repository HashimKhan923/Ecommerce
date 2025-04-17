<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;



    protected static function boot()
    {
        parent::boot();
    
        static::saved(function ($review) {
            $review->product->updateAverageRating();                   // update product rating
            $review->product->user?->updateAverageRating();         // update seller rating
        });
    
        static::deleted(function ($review) {
            $review->product->updateAverageRating();                   // update product rating
            $review->product->user?->updateAverageRating();         // update seller rating
        });
    }


    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    
}
