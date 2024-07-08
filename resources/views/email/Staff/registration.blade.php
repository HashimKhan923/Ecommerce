<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
    <img src="{{ url('emailLogo.png') }}" width="250px" alt="Company Logo" class="logo">
        <div class=" text-left"  style="margin-top:10px">
        <h3>Dragon Auto Mart</h2>
    </div>
    </div>
</div>


</div>
        <hr>
        <div class="content">
        <p class="font-size-18">Hello, Mr. {{$name}}</p>

        <p>Your account has been successfully created. Below you will find your login credentials to access our system:</p> 

        <h4>Login Credentials:</h4>
        <ul>
            <li>Email: {{$email}}</li>
            <li>Password: {{$password}}</li>
        </ul>
        <p>Visit <a href="https://seller.dragonautomart.com/login">https://seller.dragonautomart.com/login </a>and enter your credentials</p>
        

        <br><br>
        <p class="font-size-14">Regards,<br />Dragon Auto Mart</p>
        </div>
    </div>
</body>

</html>