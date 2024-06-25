<!DOCTYPE html>
<html>
<head>
    <title>Your Order Confirmation</title>
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
    <img src="{{ asset('NewEmailLogo.png') }}" width="250px" alt="Company Logo" class="logo">
        <h2>Order Confirmation</h2>
        <p>Dear {{$buyer_name}},</p>
        <br>
        <p>Your order is confirmed and is currently being prepared for delivery. You can expect your order to arrive within 4 to 5 days.</p>
        <br>
        
        
        <p>We will send you the tracking information as soon as your order is dispatched. If you have any questions or need assistance, feel free to contact our customer support at <a href="mailto:support@dragonautomart.com">support@dragonautomart.com</a> </p>
        <br>
        <p>Thank you for choosing Dragon Auto Mart!</p>
        <br>
        <p>Best regards,<br>
        Dragon Auto Mart</p>
    </div>
</body>
</html>