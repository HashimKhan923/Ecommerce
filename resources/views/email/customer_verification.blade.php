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
        <img src="https://api.dragonautomart.com/emailLogo.png" width="70" class="img-responsive" alt="Company Logo">
        <div class=" text-left"  style="margin-top:10px">
        <h3>Dragon Auto Mart</h2>
    </div>
    </div>
</div>


</div>
        <hr>
        <div class="content">
        <p class="font-size-18">Hi, Mr. {{$name}}</p>

        <p>Welcome to Dragon Auto Mart! We're excited to have you on board for this incredible journey. If you ever have any questions, concerns, or suggestions,</p> 
        <p>don't hesitate to contact us at <a href="mailto:support@dragonautomart.com">support@dragonautomart.com</a>.</p>
        <p>Click on the following button to complete your email verification procedures:</p>
        <a href="https://api.dragonautomart.com/api/verification/{{$token}}" class="btn btn-primary">Verify Email</a>
        <br><br><br>
        <p class="font-size-14">Regards,<br />Dragon Auto Mart</p>
        </div>
    </div>
    <div class="footer">
        <p>If you did not sign up for [Company Name], please ignore this email.</p>
        <p>[Company Name] | Address | Phone Number</p>
    </div>
</body>

</html>