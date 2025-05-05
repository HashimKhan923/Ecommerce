<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;



    protected static function booted()
    {
        static::created(function ($review) {
            if ($review->product && $review->product->user) {
                $review->product->user->updateAverageRating();
            }
        });
    
        static::deleted(function ($review) {
            if ($review->product && $review->product->user) {
                $review->product->user->updateAverageRating();
            }
        });
    }
    
    protected static function boot()
    {
        parent::boot();
    
        static::saved(function ($review) {
            $review->loadMissing('product');
            if ($review->product) {
                $review->product->updateAverageRating();
            }
        });
    
        static::deleted(function ($review) {
            $review->loadMissing('product');
            if ($review->product) {
                $review->product->updateAverageRating();
            }
        });
    }


    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    
}