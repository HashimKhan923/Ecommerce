<?php

namespace App\Mail;

use App\Models\Cart;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CartDiscountMail extends Mailable
{
    use SerializesModels;

    public $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function build()
    {
        return $this->subject('Good news! Your cart has a discount 🎉')
            ->markdown('email.cart_discount', [
                'cart' => $this->cart,
            ]);
    }
}
