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
            background-color: #4d4d4d; /* Dark gray background */
            height: 60px;
            padding: 15px;
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
        <table class="header" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td width="50%" align="left">
                    <div class="logo">
                        <img src="{{asset('Email/logo.webp')}}" alt="Company Logo">
                    </div>
                </td>
                <td width="50%" align="right">
                    <div class="text">
                        Automotive Marketplace
                    </div>
                </td>
            </tr>
        </table>

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
            <a href="https://dragonautomart.com/" style="display: inline-block; padding: 10px 20px; background-color: #3D772D; color: #ffffff; text-decoration: none; border-radius: 5px; font-size: 16px;">visit website</a>

            <br><br>
            <p>If you need any help, our support team is always here for you. Feel free to reach out at <a href="mailto:support@dragonautomart.com">support@dragonautomart.com</a></p>
            <br><br>
            <p class="font-size-14">Regards,<br />Dragon Auto Mart Team</p>
            </div>

        <!-- Footer Section -->
        <table class="footer" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <!-- Logo on the left -->
                <td width="50%" align="left" valign="top">
                    <div class="logo">
                        <img src="{{asset('Email/logo.webp')}}" alt="Company Logo" style="width: 150px; height: auto;">
                    </div>
                </td>
                <!-- Social icons and text on the right -->
                <td width="50%" align="right" valign="top">
                    <div class="social-section" style="padding-top: 20px;">
                        <!-- Heading aligned to the right -->
                        <div class="heading" style="text-align: right; margin-bottom: 10px;">Find us on social media platforms</div>
                        <!-- Icons in one row -->
                        <div class="social-icons" style="text-align: right; white-space: nowrap;">
                           <a href="https://web.facebook.com/dragonautomart?_rdc=1&_rdr"><img src="{{asset('Email/footerfacebook.webp')}}" alt="" width="30px" style="display: inline-block; margin-right: 10px;"></a> 
                           <a href="https://www.instagram.com/dragonautomart/"><img src="{{asset('Email/footerinsta.webp')}}" alt="" width="30px" style="display: inline-block; margin-right: 10px;"></a> 
                           <a href="https://tiktok.com/@dragonautomart"><img src="{{asset('Email/footertiktok.webp')}}" alt="" width="30px" style="display: inline-block; margin-right: 10px;"></a> 
                           <a href="https://www.X.com/dragonautomart"><img src="{{asset('Email/footerx.webp')}}" alt="" width="30px" style="display: inline-block; margin-right: 10px;"></a> 
                           <a href="https://www.youtube.com/@dragonautomart"><img src="{{asset('Email/footeryt.webp')}}" alt="" width="30px" style="display: inline-block;"></a> 
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="separator"></div>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <div class="copyright">
                        © 2025 Dragon Auto Mart, All Rights Reserved.
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>