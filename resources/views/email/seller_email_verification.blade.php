<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
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
            max-width: 100%;
            height: auto;
        }

        .company-name {
            text-align: right;
            margin-top: 10px;
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
        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <div class="logo">
                    <img src="https://dragonautomart.com/assets/logo-773651ec.webp" width="100" class="img-responsive" alt="Company Logo">
                </div>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <div class="company-name text-left"  style="margin-top:36px">
                    <h2>Dragon Auto Mart</h2>
                </div>
            </div>
        </div>
        <hr>
        <div class="content">
        <p class="font-size-18">Hi, Mr. {{$name}}</p>

        <p>Welcome to Dragon Auto Mart! We're absolutely thrilled to have you join us on this exciting journey.</p>
            <p>We're excited to let you know that your first 6-month subscription is completely free and on us. It's our way of showing appreciation for your decision to be a part of our community.</p>
            <p>To get started and learn more about how to make the most of your membership, we encourage you to explore our Sellers FAQ section. You'll find valuable information that will help you navigate your experience with Dragon Auto Mart.</p>
            <p>We're here to support you every step of the way. If you ever have any questions, concerns, or suggestions, please don't hesitate to reach out to us at <a href="mailto:support@dragonautomart.com">support@dragonautomart.com</a>. Our dedicated support team is ready to assist you.</p>
            <p>Once again, thank you for choosing Dragon Auto Mart. We can't wait to see your journey unfold on our platform.</p>
            <br>
            <a href="https://api.dragonautomart.com/api/verification/{{$token}}" class="text-white">Click Here Complete Email Verification</a>


        <br><br>
        <p class="font-size-14">Regards,<br />Dragon Auto Mart Team</p>
        </div>
    </div>

</body>

</html>