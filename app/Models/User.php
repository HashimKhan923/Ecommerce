<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

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
    ];

    public function seller_information()
    {
        return $this->hasOne(BusinessInformation::class,'seller_id','id');
    }

    public function shop()
    {
        return $this->hasOne(Shop::class,'seller_id','id');
    }

    public function SellingPlatforms()
    {
        return $this->hasMany(SellingPlatforms::class,'seller_id','id');
    }

    public function SocialPlatforms()
    {
        return $this->hasMany(SocialPlatforms::class,'seller_id','id');
    }

    public function BankDetail()
    {
        return $this->hasOne(BankDetail::class,'seller_id','id');
    }

    public function CreditCard()
    {
        return $this->hasOne(CreditCard::class,'seller_id','id');
    }
}
