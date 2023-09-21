<!DOCTYPE html>
<html>
<head>
  <title>Email Verification</title>
  <!-- Add Bootstrap CSS -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container" style="min-width:1000px; overflow:auto; line-height:2">
    <div class="row justify-content-center" style="margin:50px auto; padding:20px 0">
      <div class="col-md-8" style="border-bottom:1px solid #eee">
        <!-- Add your company logo image here -->
        <img src="https://dashboard.dragonautomart.com/assets/authlogo-9138a1c6.png" width="200" alt="Company Logo" class="img-fluid">
        <h1 class="mt-3"><a href="#" style="font-size:1.4em; color: #00466a; text-decoration:none; font-weight:600">Dragon Auto Mart</a></h1>
        <p class="font-size-18">Hi, Mr. {{$name}}</p>
        <p>Welcome to Dragon Auto Mart! We're excited to have you on board for this incredible journey. If you ever have any questions, concerns, or suggestions,</p> 
        <p>don't hesitate to contact us at <a href="mailto:support@dragonautomart.com">support@dragonautomart.com</a>.</p>
        <p>Click on the following link to complete your email verification procedures:</p>
        <h5><a href="https://api.dragonautomart.com/api/verification/{{$token}}">https://dragonautomart.com/</a></h5>
        <p class="font-size-14">Regards,<br />Dragon Auto Mart</p>
        <hr style="border:none; border-top:1px solid #eee" />
        <div class="text-right text-muted" style="padding:8px 0; font-size:0.8em; font-weight:300">
          <!-- Your company address or other information can be added here -->
        </div>
      </div>
    </div>
  </div>
</body>
</html>