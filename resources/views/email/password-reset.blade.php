<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom CSS can be added here */
        body {
            font-family: Helvetica, Arial, sans-serif;
            min-width: 1000px;
            overflow: auto;
            line-height: 2;
        }

        .container {
            margin: 50px auto;
            width: 70%;
            padding: 20px 0;
        }

        .header {
            border-bottom: 1px solid #eee;
        }

        .logo {
            max-width: 150px; /* Adjust the logo size as needed */
        }

        .greeting {
            font-size: 1.1em;
        }

        .otp-box {
            background: #00466a;
            margin: 0 auto;
            width: max-content;
            padding: 0 10px;
            color: #fff;
            border-radius: 4px;
            font-size: 1.5em;
            text-align: center;
        }

        .footer {
            font-size: 0.9em;
        }

        hr {
            border: none;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header mb-5">
            <!-- Add your company logo here -->
            <a href="#"><img src="https://dashboard.dragonautomart.com/assets/authlogo-9138a1c6.png" width="200" alt="Dragon Auto Mart Logo"></a>
            <a href="#" style="font-size: 1.4em; color: #00466a; text-decoration: none; font-weight: 600">Dragon Auto Mart</a>
        </div>
        <p class="greeting">Hi, Mr. {{$name}}</p>
        <p>Use the following OTP to complete your Reset Password procedures:</p>
        <h2 class="otp-box">{{ $token }}</h2>
        <p class="footer">Regards,<br />Dragon Auto Mart</p>
        <hr>
        <div style="float:right;padding:8px 0;color:#aaa;font-size:0.8em;line-height:1;font-weight:300">
            <!-- Add your company address and other details here -->
        </div>
    </div>

    <!-- Add Bootstrap JS if needed -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>