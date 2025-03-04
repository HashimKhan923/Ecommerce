<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Dragon Auto Mart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #dddddd;
        }
        .header {
            display: flex;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 15px;
            background-color: #4d4d4d;
            height: 60px;
        }
        .header .logo img {
            max-width: 210px;
            height: 80px;
        }
        .header .text {
            font-size: 18px;
            color: #ffffff;
        }
        .banner img {
            width: 100%;
            height: auto;
        }
        .content {
            padding: 22px;
            text-align: left;
            font-size: 14px;
        }
        .content h1 {
            font-size: 22px;
            color: #333333;
            margin-bottom: 10px;
            line-height: 1.2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: normal;
        }
        .content h1 span {
            font-size: 12px;
            color: #666666;
            font-weight: normal;
        }
        .content h2 {
            font-size: 18px;
            color: #666666;
            margin-bottom: 10px;
        }
        .content table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .content table th,
        .content table td {
            padding: 10px;
            font-size: 13px;
            text-align: left;
            border-bottom: 1px solid #dddddd;
        }
        .content table th {
            background-color: #f9f9f9;
        }
        .content table td img {
            max-width: 90px;
            height: auto;
            border-radius: 5px;
        }
        .content .totals {
           
            background-color: #f4f4f4; /* Light gray background for the container */
            padding: 5px; /* Added padding for better spacing */
            border-radius: 5px; /* Rounded corners */
        }
        .content .totals .row {
            display: flex;
            justify-content: space-between;
           
            padding: 4px; /* Added padding for better spacing */
            border-bottom: 1px solid #dddddd; /* Separator between rows */
        }
        .content .totals .row:last-child {
            border-bottom: none; /* Remove separator for the last row */
        }
        .content .totals .row strong {
            font-weight: bold;
            color: #333333;
           font-size: 13px;
        }
        .content .totals .row span {
            color: black;
          font-size: 13px;
        }
        .footer {
            text-align: left;
            padding: 20px;
            font-size: 14px;
            color: #ffffff;
            background-color: #4d4d4d;
            border-top: 1px solid #dddddd;
        }
        .footer .footer-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .footer .footer-top .logo img {
            max-width: 150px;
            height: auto;
        }
        .footer .footer-top .social-section {
            text-align: right;
            padding-top: 20px;
        }
        .footer .footer-top .social-section .heading {
            font-size: 14px;
            color: #ffffff;
            margin-bottom: 5px;
        }
        .footer .footer-top .social-icons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .footer .footer-top .social-icons img {
            width: 24px;
            height: 24px;
        }
        .footer .separator {
            border-top: 1px solid #dddddd;
            margin: 15px 0;
        }
        .footer .copyright {
            text-align: center;
            font-size: 12px;
            color: #ffffff;
        }
    </style>
</head>
@php
    use Illuminate\Support\Str;
@endphp
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">
                <img src="{{asset('Email/logo.webp')}}" alt="Company Logo">
            </div>
            <div class="text">Automotive Marketplace</div>
        </div>
        <div class="banner">
            <img src="{{asset('Email/dam_order_banner.webp')}}" alt="Welcome Banner">
        </div>
        <div class="content">
            <h1>
                Order Confirmation
               
            </h1>
            <p>Dear {{ $buyer_name }},</p>
            <p>Thank you for placing your order with Dragon auto mart. Your order has been successfully placed and is being processed. We are currently preparing it for shipment.</p>
            <p>Please allow 3-5 business days for processing and shipping due to high demand.</p>
            <p>You will receive an email with tracking information once your order has been shipped.</p>

            <h2>Order #123456</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Name</th>
                        <th>Qty</th>
                        <th>Variant</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($productsByVendor as $vendorId => $vendorProducts)
                    @foreach ($vendorProducts as $product)
                        <?php
                        $orderProduct = collect($request->products)
                            ->where('product_id', $product->id)
                            ->first();
                        
                        $price = $orderProduct['product_price'] * $orderProduct['quantity'];
                        
                        ?>
                    <tr>
                        <td>
                        @if(Str::startsWith($orderProduct['product_image'], 'https'))
                            <img src="{{ $orderProduct['product_image'] }}" alt="Product Image">
                        @else    
                             <img src="{{ 'https://api.dragonautomart.com/ProductGallery/' . $orderProduct['product_image'] }}" alt="Product Image">
                        @endif
                        </td>
                        <td>{{ $product->name }}</td>
                        <td> {{ $orderProduct['quantity'] }}</td>
                        <td>   
                            @if(isset($orderProduct['product_varient']))
                            {{ $orderProduct['product_varient'] }}
                            @endif
                        </td>
                        <td>${{ $price }}</td>
                    </tr>

                    @endforeach
                @endforeach

                </tbody>
            </table>

            <!-- Tax, Insurance, and Total -->
            <div class="totals">
            @if ($request->tax)
                <div class="row">
                    <strong>Tax({{ floatval($request->tax[2]) }}%)</strong>
                    <span>${{ floatval($request->tax[0]) }}</span>
                </div>
            @endif   
            
            @if ($request->signature)
              <div class="row">
                    <strong>Signature:</strong>
                    <span>${{ floatval($request->signature[0]) }}</span>
                </div> 
            @endif

            @if ($request->insurance)    
              <div class="row">
                    <strong>Insurance:</strong>
                    <span> ${{ floatval($request->insurance[0]) }}</span>
                </div> 
            @endif   
            
            @if ($TotalShippingAmount)
              <div class="row">
                    <strong>Shipping:</strong>
                    <span>${{ $TotalShippingAmount }}</span>
                </div>
            @endif    

            @if ($request->coupon_discount)
                <div class="row">
                    <strong>Discount:</strong>
                    <span>${{ $request->coupon_discount }}</span>
                </div>
            @endif 
                <div class="row">
                    <strong>Total:</strong>
                    <span>${{ number_format($request->amount, 2) }}</span>
                </div>
              
            </div>

            <h2>Billing Address</h2>
            <p>
            {{ $request->information[0] }},<br>  
            {{ $request->information[1] }},<br>
            {{ $request->information[2] }}, {{ $request->information[3] }},<br>
            {{ $request->information[5] }},<br>
            {{ $request->information[4] }},<br>
            {{ $request->information[6] }},<br>
            {{ $request->information[7] }}
            </p>
        </div>
        <!-- Footer Section -->
        <div class="footer">
            <div class="footer-top">
                <!-- Logo on the left -->
                <div class="logo">
                    <img src="{{asset('Email/logo.webp')}}" alt="Company Logo">
                </div>
                <!-- Heading and icons on the right -->
                <div class="social-section">
                    <div class="heading">Find us on social media platforms:</div>
                    <div class="social-icons">
                    <img src="{{asset('Email/footerfacebook.webp')}}" alt="Facebook">
                        <img src="{{asset('Email/footerinsta.webp')}}" alt="Twitter">
                        <img src="{{asset('Email/footertiktok.webp')}}" alt="Instagram">
                        <img src="{{asset('Email/footerx.webp')}}" alt="LinkedIn">
                        <img src="{{asset('Email/footeryt.webp')}}" alt="YouTube">
                    </div>
                </div>
            </div>
            <div class="separator"></div>
            <div class="copyright">
                © 2025 Dragon Auto Mart, All Rights Reserved.
            </div>
        </div>
    </div>
</body>
</html>