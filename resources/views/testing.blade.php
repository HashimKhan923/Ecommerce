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
            align-items: center;
            justify-content: space-between;
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
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 14px;
            color: #ffffff;
            background-color: #4d4d4d;
            border-top: 1px solid #dddddd;
        }
        .footer a {
            color: #31c48d;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">
                <img src="https://skartistic.com/wp-content/uploads/2025/03/Screenshot-2025-03-04-015736.png" alt="Company Logo">
            </div>
            <div class="text">Automotive Marketplace</div>
        </div>
        <div class="banner">
            <img src="https://skartistic.com/wp-content/uploads/2025/03/Dam-order-banner.png" alt="Welcome Banner">
        </div>
        <div class="content">
            <h1>
                Order Confirmation
                <!-- <span>Date: March 4, 2025</span> -->
            </h1>
            <p>Hello {{ $buyer_name }},</p>
            <p>Thank you for placing your order with Dragon auto mart. Your order has been successfully placed and is being processed. We are currently preparing it for shipment.</p>
            <p>Please allow 3-5 business days for processing and shipping due to high demand.</p>
            <p>You will receive an email with tracking information once your order has been shipped.</p>

            <h2>Order #123456</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Variant</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><img src="https://skartistic.com/wp-content/uploads/2025/03/d3.webp" alt="Product Image"></td>
                        <td>HRS 2009-24 Nissan GT-R R35 LED Tail Lights - The Elite Series</td>
                        <td>2</td>
                        <td>DRL</td>
                        <td>$25.00</td>
                    </tr>

                </tbody>
            </table>

            <h2>Billing Address</h2>
            <p>
                Name<br>  
                Address<br>
                Country, City<br>
                Phone <br>
                Email 
            </p>
        </div>
        <div class="footer">
            <p>If you have any questions, feel free to <a href="mailto:support@example.com">contact us</a>.</p>
            <p>You are receiving this email because you signed up on our website. <a href="#">Unsubscribe</a>.</p>
        </div>
    </div>
</body>
</html>