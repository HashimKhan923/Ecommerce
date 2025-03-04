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
            align-items: center !important;
            justify-content: space-between !important;
            padding: 15px;
            background-color: #4d4d4d; /* Dark gray background */
            height: 60px;
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
            text-align: left;
            padding: 20px;
            font-size: 14px;
            color: #ffffff; /* White text color */
            background-color: #4d4d4d; /* Dark gray background */
            border-top: 1px solid #dddddd;
        }
        .footer .footer-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .footer .footer-top .logo img {
            max-width: 150px;
            height: auto;
        }
        .footer .footer-top .social-section {
            text-align: right;
            padding-top: 20px; /* Added padding to move heading and icons down */
        }
        .footer .footer-top .social-section .heading {
            font-size: 14px;
            color: #ffffff;
            margin-bottom: 5px;
        }
        .footer .footer-top .social-icons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .footer .footer-top .social-icons img {
            width: 24px;
            height: 24px;
        }
        .footer .separator {
            border-top: 1px solid #dddddd;
            margin: 15px 0;
        }
        .footer .copyright {
            text-align: center;
            font-size: 12px;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header Section -->
        <div class="header">
            <div class="logo">
                <img src="{{asset('Email/logo.webp')}}" alt="Company Logo">
            </div>
            <div class="text">
                Automotive Marketplace
            </div>
        </div>

        <!-- Banner Section -->
        <div class="banner">
            <img src="{{asset('Email/dam_welcome_banner.webp')}}" alt="Welcome Banner">
        </div>

        <!-- Email Content Section -->
        <div class="content">
            <!-- <h1>Welcome to Dragon Auto Mart</h1> -->
            <p>Hello {{$name}},</p>
            <p>Welcome to Dragon Auto Mart, your trusted source for high-quality automotive parts and accessories. Find everything you need to keep your vehicle running at its best, all in one place.</p>
            <p>With your new account, you can:</p>
            <ul>
                <li>Browse and shop from a wide range of products available in our store</li>
                <li>Track your orders and manage your purchases easily</li>
                <li>Save your favorite items for later</li>
                <li>Get exclusive deals and personalized recommendations</li>
            </ul>
            <a href="https://dragonautomart.com/" class="cta-button">visit website</a>
            <br><br>
            <p>If you need any help, our support team is always here for you. Feel free to reach out at <a href="mailto:support@dragonautomart.com">support@dragonautomart.com</a></p>
            <p>Regards, Dragon Auto Mart</p>
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <div class="footer-top">
                <!-- Logo on the left -->
                <div class="logo">
                    <img src="{{asset('Email/logo.webp')}}" alt="Company Logo">
                </div>
                <!-- Heading and icons on the right -->
                <div class="social-section">
                    <div class="heading">Find us on social media platforms</div>
                    <div class="social-icons">
                        <img src="{{asset('Email/footerfacebook.webp')}}" alt="Facebook">
                        <img src="{{asset('Email/footerinsta.webp')}}" alt="Twitter">
                        <img src="{{asset('Email/footertiktok.webp')}}" alt="Instagram">
                        <img src="{{asset('Email/footerx.webp')}}" alt="LinkedIn">
                        <img src="{{asset('Email/footeryt.webp')}}" alt="YouTube">
                    </div>
                </div>
            </div>
            <div class="separator"></div>
            <div class="copyright">
                © 2025 Dragon Auto Mart, All Rights Reserved.
            </div>
        </div>
    </div>
</body>
</html>