<!DOCTYPE html>
<html>

<head>
    <title>Your Order Confirmation</title>
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
        <h2>Order Confirmation</h2>
        <p>Dear {{ $buyer_name }},</p>
        <br>
        <p>Your order is confirmed and is currently being prepared for delivery. You can expect your order to arrive
            within 4 to 5 days.</p>
        <br>


        <p>We will send you the tracking information as soon as your order is dispatched. If you have any questions or
            need assistance, feel free to contact our customer support at <a
                href="mailto:support@dragonautomart.com">support@dragonautomart.com</a> </p>
        <br>
        <p>Thank you for choosing Dragon Auto Mart!</p>
        <br>
        <p>Best regards,<br>
            Dragon Auto Mart</p>
    </div>
</body>

</html>
