<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exclusive Discount Just for You!</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .logo {
            text-align: center;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .logo img {
            max-width: 250px;
        }
        .shop_logo {
            text-align: center;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .shop_logo img {
            max-width: 150px;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 20px;
        }
        .header h1 {
            margin: 0;
        }
        .content {
            padding: 20px;
        }
        .store-details {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }
        .store-details h2 {
            color: #4CAF50;
            margin-bottom: 10px;
        }
        .product {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .product img {
            max-width: 100px;
            border-radius: 8px;
        }
        .product-details {
            flex: 1;
            margin-left: 20px;
        }
        .product-details h5 {
            color: #4CAF50;
        }
        .coupon-details {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-top: 20px;
        }
        .coupon-details h2 {
            margin-bottom: 15px;
            color: #4CAF50;
        }
        .coupon-code {
            font-size: 24px;
            font-weight: bold;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .footer {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 10px;
        }
        .footer a {
            color: white;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Logo Section Start -->
        <div class="logo">
            <img src="{{ asset('emailLogo.png') }}" alt="Company Logo">
        </div>
        <!-- Logo Section End -->
        <div class="header">
            <h1>🎉 Special Discount Just for You! 🎉</h1>
        </div>
        <div class="content">
            



<p>Dear Valued Customer,</p>
<p>We are excited to offer you an exclusive discount on the following products:</p>

            <!-- Coupon Details Section Start -->
            <div class="coupon-details">
                <h2>Your Coupon Details</h2>
                <p>Use the coupon code below at checkout to avail your discount:</p>
                <div class="coupon-code">{{$coupon->code}}</div>

                @if($coupon->minimum_purchase_amount)
                <p><strong>Required Minimum Order Amount:</strong> ${{$coupon->minimum_purchase_amount}}</p>
                @elseif($coupon->minimum_quantity_items)
                <p><strong>Required Minimum Items qty:</strong> {{$coupon->minimum_quantity_items}}</p>
                @else

                @endif
                <p><strong>Validity:</strong> Until {{ $coupon->end_date->format('jS F Y') }}</p>
                <p><strong>Terms and Conditions:</strong> Applicable on select products only.</p>
            </div>
            <!-- Coupon Details Section End -->
            
<!-- Product Section Start -->
@foreach($products as $product)
    <div class="product">
    <img src="https://api.dragonautomart.com/ProductGallery/{{$product->product_single_gallery->image}}" alt="Product Image">
    <div class="product-details">
        <h5>{{$product->name}}</h5>
        <p><strong>Discount:</strong>
            @if($coupon->discount_type == 'percentage')
                {{$coupon->discount}}%
            @else
                ${{$coupon->discount}}
            @endif
            off
        </p>
        <a href="https://dragonautomart.com/product/{{$product->id}}" class="btn btn-danger btn-sm">Add To Cart</a>
    </div>
    </div>
@endforeach
<!-- Product Section End -->


        </div>
        <div class="footer">
            <p>&copy; 2024 Dragon Auto Mart. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
