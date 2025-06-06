<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Laravel\Scout\Searchable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     * 
     */

     
    protected $fillable = [
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'permissions' => 'array'
    ];

    public function seller_information()
    {
        return $this->hasOne(BusinessInformation::class,'seller_id','id');
    }

    public function shop()
    {
        return $this->hasMany(Shop::class,'seller_id','id');
    }

    public function SellingPlatforms()
    {
        return $this->hasMany(SellingPlatforms::class,'seller_id','id');
    }

    public function SocialPlatforms()
    {
        return $this->hasMany(SocialPlatforms::class,'seller_id','id');
    }

    public function FeaturedProduct()
    {
        return $this->hasMany(FeaturedProductOrder::class,'seller_id','id');
    }

    public function ProductListing()
    {
        return $this->hasMany(ProductListingPayment::class,'seller_id','id');
    }

    public function BankDetail()
    {
        return $this->hasOne(BankDetail::class,'seller_id','id');
    }

    public function CreditCard()
    {
        return $this->hasOne(CreditCard::class,'seller_id','id');
    }

    public function order()
    {
        return $this->hasMany(Order::class,'customer_id','id');
    }

    public function seller_order()
    {
        return $this->hasMany(Order::class,'sellers_id','id');
    }

    public function time_line()
    {
        return $this->hasMany(OrderTimeline::class,'customer_id','id');
    }

    public function seller_time_line()
    {
        return $this->hasMany(OrderTimeline::class,'seller_id','id')->where('customer_id',null)->where('order_id',null);
    }

    public function staf()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function stafs()
    {
        return $this->hasMany(User::class, 'seller_id');
    }

    public function my_customers()
    {
        return $this->hasMany(MyCustomer::class, 'seller_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'user_id','id');
    }

    public function productReviews()
    {
        return $this->hasManyThrough(
            \App\Models\Review::class,
            \App\Models\Product::class,       
            'user_id',                       
            'product_id',                     
            'id',                             
            'id'                               
        );
    }

    public function updateAverageRating()
    {
        $average = $this->productReviews()->avg('average_rating');
        $this->average_rating = $average ?? 0;  // fallback if no reviews yet
        $this->save();
    }
}