<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us Information</title>
    <style>
        /* Inline CSS styles to ensure email client compatibility */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
        }

        h1 {
            color: #333;
        }

        p {
            color: #555;
        }

        .user-info {
            margin: 20px 0;
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
        <br>
        <h1>Contact Us Information</h1>
        <div class="user-info">
            <p><strong>Email:</strong> {{$email}}</p>
            <p><strong>Subject:</strong> {{$subject}}</p>
            <p><strong>Message:</strong> {{$message1}}</p>
        </div>
    </div>
</body>
</html>