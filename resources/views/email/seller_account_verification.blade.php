<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        <div class="logo">
            <img src="https://api.dragonautomart.com/gmail_banner.png" alt="Company Logo">
        </div>

        <div class="content">
            <p class="font-size-18">Hello, {{ $name }}</p>

            <p class="card-text">We are pleased to inform you that your account on our platform has been verified and
                activated by our admin team.</p>
            <p class="card-text">You can now start using your account to list your products, manage orders, and access
                all the features of our platform.</p>
            <p class="card-text">If you have any questions or need assistance, please don't hesitate to contact our
                support team.</p>
            <p class="card-text">Thank you for choosing our platform for your selling needs!</p>

            <a href="https://seller.dragonautomart.com/">Click here to visit your Seller Dashboard</a>


            <br><br>
            <p class="font-size-14">Regards,<br />Dragon Auto Mart</p>
        </div>
    </div>

</body>

</html>
