<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Cart Update</title>
</head>
<body style="margin:0; padding:0; background-color:#f7f7f7; font-family:Arial, sans-serif;">
  <table align="center" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 5px rgba(0,0,0,0.1); margin-top:20px;">
    
    <!-- Header -->
    <tr>
      <td align="center" style="background:#4CAF50; padding:20px; color:#fff; font-size:24px; font-weight:bold;">
        Special Update About Your Cart
      </td>
    </tr>

    <!-- Greeting -->
    <tr>
      <td style="padding:20px; font-size:16px; color:#333;">
        <p>Hello,</p>
        <p>
          You left some items in your cart.<br>
          <strong style="color:#4CAF50;">
            {{ $cart->discount_reason === 'manual' ? 'The seller gave you a discount!' : 'One of your products dropped in price!' }}
          </strong>
        </p>
      </td>
    </tr>

    <!-- Cart Items -->
    <tr>
      <td style="padding:0 20px 20px 20px;">
        <table width="100%" cellpadding="0" cellspacing="0">
          @foreach($cart->items as $item)
          <tr style="border-bottom:1px solid #eee;">
            <td width="100" style="padding:10px;">
              <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" width="90" style="border-radius:6px; display:block;">
            </td>
            <td style="padding:10px; vertical-align:top;">
              <div style="font-size:15px; font-weight:bold; color:#333;">{{ $item->product->name }}</div>
              <div style="margin-top:5px; font-size:14px; color:#777;">
                <span style="text-decoration:line-through; color:#999;">${{ number_format($item->old_price, 2) }}</span>
                <span style="color:#e53935; font-weight:bold; margin-left:8px;">${{ number_format($item->new_price, 2) }}</span>
              </div>
              <div style="margin-top:5px; font-size:13px; color:#666;">Qty: {{ $item->quantity }}</div>
            </td>
          </tr>
          @endforeach
        </table>
      </td>
    </tr>

    <!-- New Total -->
    <tr>
      <td style="padding:20px; text-align:center; background:#f9f9f9;">
        <div style="font-size:16px; color:#333; margin-bottom:8px;">
          <strong>New Total:</strong>
          <span style="color:#e53935; font-weight:bold;">
            ${{ number_format($cart->total_amount - $cart->discount_amount, 2) }}
          </span>
        </div>
        <div style="font-size:14px; color:#777;">
          You saved <strong style="color:#4CAF50;">${{ number_format($cart->discount_amount, 2) }}</strong> 🎉
        </div>
      </td>
    </tr>

    <!-- CTA Button -->
    <tr>
      <td align="center" style="padding:30px;">
        <a href="{{ config('app.frontend_url') . "/cart/{$cart->id}" }}" 
           style="background:#4CAF50; color:#fff; text-decoration:none; padding:14px 28px; 
                  border-radius:6px; font-size:16px; font-weight:bold; display:inline-block;">
          Complete Your Purchase
        </a>
      </td>
    </tr>

    <!-- Footer -->
    <tr>
      <td align="center" style="padding:20px; font-size:13px; color:#999; background:#f1f1f1;">
        Thanks,<br> Team
      </td>
    </tr>

  </table>
</body>
</html>
