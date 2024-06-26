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
        <img src="https://seller.dragonautomart.com/assets/logo-ea477fb2.png" width="70" class="img-responsive" alt="Company Logo">
        <div class=" text-left"  style="margin-top:10px">
        <h3>Dragon Auto Mart</h2>
    </div>
    </div>
</div>


</div>
        <hr>
        <div class="content">
        <p class="font-size-18">Hello, Mr. {{$Customer->name}}</p>
<hr>

        <!-- <div>
        <img src="{{ asset('EmailLogo.png') }}" width="200" alt="Company Logo" class="logo">
  <h4>{{$ShopName}}</h4>
</div> -->



<p> <strong>Reply: </strong> {{$Msg}} </p>

<br>
 <a href="https://dragonautomart.com/messages" style="display: inline-block; padding: 10px 20px; background-color: green; color: #fff; text-align: center; text-decoration: none; border: none; border-radius: 5px; cursor: pointer;">Reply</a>


<hr>


        <p class="font-size-14">Regards, <br />Dragon Auto Mart</p>
        </div>
    </div>

</body>

</html>