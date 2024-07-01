<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exclusive Discount Just for You!</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
            <img src="{{ asset('EmailLogo.png') }}" alt="Company Logo">
        </div>
        <!-- Logo Section End -->
        <div class="header">
            <h1>Special Discount Just for You!</h1>
        </div>
        <div class="content">
            

            <div class="shop_logo">
    <img src="{{ asset('EmailLogo.png') }}" alt="Company Logo">
    <h4 style="color: #4CAF50;">Your Store Name</h4>
</div>

<p>Dear Valued Customer,</p>
            <p>We are excited to offer you an exclusive discount on the following products:</p>
            
            <!-- Product Section Start -->
            <div class="product">
                <img src="https://via.placeholder.com/100" alt="Product Image">
                <div class="product-details">
                    <h5>Product Name 1</h5>
                    <p><strong>Discount:</strong> 20% off</p>
                </div>
            </div>

            <div class="product">
                <img src="https://via.placeholder.com/100" alt="Product Image">
                <div class="product-details">
                    <h5>Product Name 2</h5>
                    <p><strong>Discount:</strong> 25% off</p>
                </div>
            </div>
            <!-- Product Section End -->

            <!-- Coupon Details Section Start -->
            <div class="coupon-details">
                <h2>Your Coupon Details</h2>
                <p>Use the coupon code below at checkout to avail your discount:</p>
                <div class="coupon-code">DISCOUNT2024</div>
                <p><strong>Validity:</strong> Until June 30, 2024</p>
                <p><strong>Terms and Conditions:</strong> Applicable on select products only.</p>
            </div>
            <!-- Coupon Details Section End -->
        </div>
        <div class="footer">
            <p>&copy; 2024 Dragon Auto Mart. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
