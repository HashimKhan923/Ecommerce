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
            <p style="font-size: 18px;">Hello, {{ $name }}</p>

            <p>Welcome to Dragon Auto Mart! We're excited to have you on board for this incredible journey. If you ever
                have any questions, concerns, or suggestions,</p>
            <p>don't hesitate to contact us at <a
                    href="mailto:support@dragonautomart.com">support@dragonautomart.com</a>.</p>
            <p>Click on the following button to complete your email verification procedures:</p>

            <!-- Inline styled button -->
            <a href="https://api.dragonautomart.com/api/verification/{{ $token }}" 
               style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 5px; font-size: 16px;">
               Verify Email
            </a>

            <br><br><br>
            <p style="font-size: 14px;">Regards,<br />Dragon Auto Mart</p>
        </div>
    </div>
</body>

</html>
