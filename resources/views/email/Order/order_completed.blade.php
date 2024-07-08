<!DOCTYPE html>
<html>
<head>
    <title>Your Order is Completed</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px 0px #888;
        }

        h2 {
            color: #333;
        }

        p {
            margin: 0;
            color: #777;
        }

        strong {
            color: #333;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
        }

        .logo {
            max-width: 100px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="https://api.dragonautomart.com/emailLogo.png" width="250px" alt="Company Logo" class="logo">
        <h2>Your Order is Completed</h2>
        <p>Dear {{$buyer_name}},</p>
        <br>
        <p>Thank you for your order from {{$shop->name}}! We wanted to let you know that your order (#{{$order->id}}) was shipped via {{$TrackingOrder->courier_name}}, on {{$date}}.</p>
        <br>
        <p>Your Order Tracking Number is <b><strong>{{$TrackingOrder->tracking_number}}</strong></b> You can track your package at any time using the button below.</p>
        <br>
        <a href="{{$TrackingOrder->courier_link}}" class="btn btn-info">Track My Order</a>
        <br>
        <p>Thank you for choosing Dragon Auto Mart!</p>
        <br>
        
        <p>Best regards,<br>
        Dragon Auto Mart</p>
    </div>
</body>
</html>