<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
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
            <p class="font-size-18">Hello, {{ $vendor_name }}</p>

            <p class="card-text">We are pleased to inform you that a payout of {{ $amount }} has been processed.
            </p>
            <p class="card-text">The funds will be transferred to your account within 3 to 5 business days.</p>
            <p class="card-text">If you have any questions, please contact us at <a
                    href="mailto:support@dragonautomart.com">support@dragonautomart.com</a>.</p>
            <p class="card-text">Thank you for choosing our platform for your selling needs!</p>



            <br><br>
            <p class="font-size-14">Regards,<br />Dragon Auto Mart</p>
        </div>
    </div>

</body>

</html>
