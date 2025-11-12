<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    protected $casts = [
        'tags' => 'array',
    ];

    protected $fillable = [
        'category',
        'title',
        'description',
        'year',
        'brand_id',
        'model_id',
        'color',
        'kms_driven',
        'fuel_type',
        'transmission',
        'mileage',
        'engine_capacity',
        'registration_number',
        'ownership_type',
        'insurance_validity',
        'price',
        'negotiable',
        'condition',
        'location',
        'contact_number',
        'email',
        'tags',
        'seller_id',
        'published',
        'featured',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function model()
    {
        return $this->belongsTo(Model::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function galleries()
    {
        return $this->hasMany(AdGallery::class);
    }

    


}
