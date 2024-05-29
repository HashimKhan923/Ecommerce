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
    <img src="{{ asset('emailLogo.png') }}" width="200" alt="Company Logo" class="logo">
        <div class=" text-left"  style="margin-top:10px">
        <h3>Dragon Auto Mart</h2>
    </div>
    </div>
</div>


</div>
        <hr>
        <div class="content">
            <b>Hi [Customer's Name],</b>

                <p>We're excited to offer you an exclusive [Discount]% off on your next purchase!</p>

                <p>Your Coupon Code: [COUPONCODE]</p>

                <p>How to Redeem:</p>
                <ol>
                    <li>Visit [Website URL].</li>
                    <li>Add your favorite items to the cart.</li>
                    <li>Enter [COUPONCODE] at checkout.</li>
                    <li>Enjoy your savings!</li>
                </ol>
                
                
                
                
                <p>Hurry! This offer is valid until [Expiry Date].</p>

                <p>Thank you for being a valued customer. If you have any questions, feel free to contact us.</p>

                <p>Happy shopping!</p>

                <p>Best regards,</p>
                <p>[Your Company Name]</p>
                <p>[Contact Information]</p>
                <p>[Website URL]</p>

        </div>
    </div>

</body>

</html>