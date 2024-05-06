<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyCustomer extends Model
{
    use HasFactory;

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
        return $this->belongsToMany(Order::class, 'orders', 'customer_id', 'customer_id')
                    ->withPivot('customer_id as pivot_customer_id');
    }
    
}
