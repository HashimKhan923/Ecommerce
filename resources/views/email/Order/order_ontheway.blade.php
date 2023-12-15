<!DOCTYPE html>
<html>
<head>
    <title>Your Order Is On the Way</title>
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
        <img src="https://dashboard.dragonautomart.com/assets/authlogo-9138a1c6.png" width="200" alt="Company Logo" class="logo">
        <h2>Your Order Is On the Way</h2>
        <br>
        <h3>Tracking Number: {{$TrackingOrder->tracking_number}}</h3>
        <br>
        <p>Dear {{$buyer_name}},</p>
        <br>
        <p>We are excited to inform you that your order is now on its way to your delivery address by {{$TrackingOrder->courier_name}}. You can expect your order to arrive within the estimated delivery time frame.</p>
        <br>
        <p>If you have any questions or need assistance with your order, please feel free to contact our customer support at <a href="mailto:support@dragonautomart.com">support@dragonautomart.com</a>.</p>
        <br>
        <p>Thank you for choosing Dragon Auto Mart!</p>
        <br>
        <p>Best regards,<br>
        Dragon Auto Mart</p>
    </div>
</body>
</html>