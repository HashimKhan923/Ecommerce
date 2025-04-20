<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Dragon Auto Mart</title>
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
            padding: 22px;
            text-align: left;
            font-size: 14px;
        }
        .content h1 {
            font-size: 22px;
            color: #333333;
            margin-bottom: 10px;
            line-height: 1.2;
            font-weight: normal;
        }
        .content h1 span {
            font-size: 12px;
            color: #666666;
            font-weight: normal;
        }
        .content h2 {
            font-size: 18px;
            color: #666666;
            margin-bottom: 10px;
        }
        .content table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .content table th,
        .content table td {
            padding: 10px;
            font-size: 13px;
            text-align: left;
            border-bottom: 1px solid #dddddd;
        }
        .content table th {
            background-color: #f9f9f9;
        }
        .content table td img {
            max-width: 90px;
            height: auto;
            border-radius: 5px;
        }
        .content .totals {
            background-color: #f4f4f4;
            padding: 5px;
            border-radius: 5px;
        }
        .content .totals table {
            width: 100%;
            border-collapse: collapse;
        }
        .content .totals table td {
            padding: 4px;
            border-bottom: 1px solid #dddddd;
        }
        .content .totals table td:last-child {
            text-align: right;
        }
        .content .totals table tr:last-child td {
            border-bottom: none;
        }
        .content .totals table strong {
            font-weight: bold;
            color: #333333;
            font-size: 13px;
        }
        .content .totals table span {
            color: black;
            font-size: 13px;
        }
        .footer {
            text-align: left;
            padding: 20px;
            font-size: 14px;
            color: #ffffff;
            background-color: #4d4d4d;
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
            padding-top: 20px;
        }
        .footer .footer-top .social-section .heading {
            font-size: 14px;
            color: #ffffff;
            margin-bottom: 5px;
        }
        .footer .footer-top .social-icons {
            display: inline-block;
        }
        .footer .footer-top .social-icons img {
            width: 24px;
            height: 24px;
            margin-left: 10px;
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
                    <div class="text">Automotive Marketplace</div>
                </td>
            </tr>
        </table>

        <!-- Banner Section -->
        <div class="banner">
            <img src="{{asset('Email/password-reset-otp-dam-banner.png')}}" alt="Welcome Banner">
        </div>

        <!-- Content Section -->
        <div class="content">
            <h1>Reset Your Password</h1>
                <p>Hello {{ $name }},</p>
                <p>As requested, here’s your OTP code to Reset your Password:</p>
                <a href="#" class="reset-button">{{ $token }}</a>
                <p>Do not share it with anyone.</p>
            <p> If you didn't ask us to reset your password, reset it now to keep your account secure. </p>
            <p> Need help? Contact support or reply to this email.</p>
    
                <p>Regards,<br>Dragon Auto Mart</p>
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
                        <div class="heading" style="text-align: right; margin-bottom: 10px;">Find us on social media platforms</div>
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