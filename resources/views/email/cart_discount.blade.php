@component('mail::message')
# Special Update About Your Cart

Hello,  

You left some items in your cart.  
**{{ $cart->discount_reason === 'manual' ? 'The seller gave you a discount!' : 'One of your products dropped in price!' }}**

---

## Your Cart Items
@component('mail::table')
| Product | Old Price | New Price | Qty |
|---------|-----------|-----------|-----|
@foreach($cart->items as $item)
| ![{{ $item->product->name }}]({{ $item->product->image_url }}) | <del>${{ number_format($item->old_price, 2) }}</del> | **${{ number_format($item->new_price, 2) }}** | {{ $item->quantity }} |
@endforeach
@endcomponent

---

## 🎉 Great News!
**New Total:** ${{ number_format($cart->total_amount - $cart->discount_amount, 2) }}  
You saved **${{ number_format($cart->discount_amount, 2) }}**!

---

@component('mail::button', ['url' => config('app.frontend_url') . "/cart/{$cart->id}", 'color' => 'success'])
Complete Your Purchase
@endcomponent

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
