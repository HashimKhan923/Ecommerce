<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Chat;

class Chat extends Model
{
    use HasFactory;


    public function seller()
    {
        return $this->belongsTo(User::class,'seller_id','id');
    } 

    public function customer()
    {
        return $this->belongsTo(User::class,'customer_id','id');
    } 

    public function shop()
    {
        return $this->belongsTo(Shop::class,'shop_id','id');
    } 

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id','id');
    } 

    public function my_customer()
    {
        return $this->hasManyThrough(MyCustomer::class, Chat::class, 'customer_id', 'customer_id')
                    ->where('my_customers.seller_id', '=', $this->seller_id)
                    ->whereColumn('chats.customer_id', '=', 'my_customers.customer_id');
    }
}
