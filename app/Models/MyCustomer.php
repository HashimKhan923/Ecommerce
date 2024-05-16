<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyCustomer extends Model
{
    use HasFactory;

    protected $fillable = ['seller_id','customer_id','sale'];

    public function customer()
    {
        return $this->belongsTo(User::class,'customer_id','id');
    } 

    public function subscriber()
    {
        return $this->hasOne(Subscriber::class,'customer_id','customer_id');
    }
    
    public function orders()
    {
        return $this->hasMany(Order::class,'customer_id','customer_id');
    }
}
