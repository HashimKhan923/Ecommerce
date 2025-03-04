<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Store!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #dddddd;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            background-color: #4d4d4d; /* Dark gray background */
            height: 60px
        }
        .header .logo img {
            max-width: 210px;
            height: 80px;
        }
        .header .text {
            font-size: 18px;
            color: #ffffff; /* White text color */
            font-weight: normal;
        }
        .banner img {
            width: 100%;
            height: auto;
        }
        .content {
            padding: 20px;
            text-align: left;
        }
        .content h1 {
            font-size: 24px;
            color: #333333;
            margin-bottom: 20px;
        }
        .content p {
            font-size: 16px;
            color: #666666;
            line-height: 1.6;
        }
        .content ul {
            margin: 20px 0;
            padding-left: 20px;
        }
        .content ul li {
            margin-bottom: 10px;
            font-size: 16px;
            color: #666666;
        }
        .cta-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            color: #ffffff;
            background-color: #3d772d; /* Green button color */
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 14px;
            color: #ffffff; /* White text color */
            background-color: #4d4d4d; /* Dark gray background */
            border-top: 1px solid #dddddd;
        }
        .footer a {
            color: #31c48d; /* Green link color */
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header Section -->
        <div class="header">
            <div class="logo">
                <img src="https://skartistic.com/wp-content/uploads/2025/03/Screenshot-2025-03-04-015736.png" alt="Company Logo">
            </div>
            <div class="text">
            Automotive Marketplace
            </div>
        </div>

        <!-- Banner Section -->
        <div class="banner">
            <img src="https://skartistic.com/wp-content/uploads/2025/03/Dam-welcome-banner.png" alt="Welcome Banner">
        </div>

        <!-- Email Content Section -->
        <div class="content">
            <h1>Welcome to Dragon Auto Mart</h1>
            <p>Hello {{$name}},</p>
            <p>Welcome to Dragon Auto Mart your trusted source for high quality automotive parts and accessories. Find everything you need to keep your vehicle running at its best, all in one place.</p>
            <p>With your new account, you can:</p>
            <ul>
                <li>Browse and shop from a wide range of products available in our store</li>
                <li>Track your orders and manage your purchases easily</li>
                <li>Save your favorite items for later</li>
                <li>Get exclusive deals and personalized recommendations</li>
            </ul>
            
            <!-- <a href="#" class="cta-button">Login to Your Account</a> -->
          <Br></Br>
            <p>If you need any help, our support team is always here for you. Feel free to reach out at <a href="mailto:support@example.com">support@dragonautomart.com</a> 
            <p>TEAM DAM</p>
      
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <p>If you have any questions, feel free to <a href="mailto:support@dragonautomart.com">contact us</a>.</p>
            <!-- <p>You are receiving this email because you signed up on our website. <a href="#">Unsubscribe</a>.</p> -->
        </div>
    </div>
</body>
</html>