<!DOCTYPE html>
<html>

<head>
    <title>Your Order is Completed</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .logo img {
            width: 100%;
            height: auto;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            display: block;
        }

        .content {
            margin-top: 20px;
        }

        .verification-link {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            color: #888888;
        }
    </style>
</head>

<body>
    <div class="container">
        <img src="https://api.dragonautomart.com/gmail_banner.png" alt="Company Logo">
        <h2>Your Order is Completed</h2>
        <p>Dear {{ $buyer_name }},</p>
        <br>
        <p>Thank you for your order from {{ $shop->name }}! We wanted to let you know that your order
            (#{{ $order->id }}) was shipped via {{ $TrackingOrder->courier_name }}, on {{ $date }}.</p>
        <br>
        <p>Your Order Tracking Number is <b><strong>{{ $TrackingOrder->tracking_number }}</strong></b> You can track
            your package at any time using the button below.</p>
        <br>
        <a href="{{ $TrackingOrder->courier_link }}" class="btn btn-info">Track My Order</a>
        <br>
        <p>Thank you for choosing Dragon Auto Mart!</p>
        <br>

        <p>Best regards,<br>
            Dragon Auto Mart</p>
    </div>
</body>

</html>
