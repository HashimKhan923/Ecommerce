<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - Dragon Auto Mart</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            min-width: 1000px;
            overflow: auto;
            line-height: 1.6;
        }

        .container {
            margin: 50px auto;
            width: 70%;
            padding: 20px 0;
        }

        .header {
            border-bottom: 1px solid #eee;
            text-align: center;
        }

        .header a {
            font-size: 1.4em;
            color: #00466a;
            text-decoration: none;
            font-weight: 600;
        }

        .content {
            font-size: 1.1em;
        }

        .content p {
            margin-bottom: 10px;
        }

        .cta-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #00466a;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        .signature {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="#"><img src="https://dashboard.dragonautomart.com/assets/authlogo-9138a1c6.png" width="200" alt="Dragon Auto Mart Logo"></a>
        </div>
        <div class="content mt-5">
            <p>Welcome to Dragon Auto Mart! We're absolutely thrilled to have you join us on this exciting journey.</p>
            <p>We're excited to let you know that your first 6-month subscription is completely free and on us. It's our way of showing appreciation for your decision to be a part of our community.</p>
            <p>To get started and learn more about how to make the most of your membership, we encourage you to explore our Sellers FAQ section. You'll find valuable information that will help you navigate your experience with Dragon Auto Mart.</p>
            <p>We're here to support you every step of the way. If you ever have any questions, concerns, or suggestions, please don't hesitate to reach out to us at <a href="mailto:support@dragonautomart.com">support@dragonautomart.com</a>. Our dedicated support team is ready to assist you.</p>
            <p>Once again, thank you for choosing Dragon Auto Mart. We can't wait to see your journey unfold on our platform.</p>
            <p>Best regards,</p>
            <p class="signature">The Dragon Auto Mart Team</p>
        </div>
        <div class="cta-button text-center">
            <a href="https://api.dragonautomart.com/api/verification/{{$token}}" class="text-white">Click Here Complete Email Verification</a>
        </div>
    </div>
</body>
</html>