<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Review - Dragon Auto Mart</title>
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
        background-color: #4d4d4d;
        /* Dark gray background */
        height: 60px;
        padding: 15px;
    }

    .header .logo img {
        max-width: 210px;
        height: 80px;
    }

    .header .text {
        font-size: 18px;
        color: #ffffff;
        /* White text color */
        font-weight: normal;
    }

    .banner img {
        width: 100%;
        height: auto;
    }

    .content {
        padding-left: 15px;
        padding-right: 15px;
        padding-bottom: 15px;
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

    .content .review-container {
        background-color: #fffae9;
        /* Background color for review section */
        padding: 15px;
        /* Added padding for spacing */
        border: 0.5px solid black;
        /* Black border */
        border-radius: 5px;
        /* Optional: Rounded corners */
        margin-top: 20px;
        /* Spacing from the content above */
    }

    .content .review-section {
        margin-top: 0;
        /* Reset margin for review section */
    }

    .content .review-section table {
        width: 100%;
        border-collapse: collapse;
    }

    .content .review-section table td {
        padding: 10px 10px 10px 0;
        /* Adjusted left padding to 0 */
        vertical-align: top;
    }

    .content .review-section table td.product-image img {
        width: 120px;
        /* Increased image width */
        height: auto;
        border-radius: 5px;
    }

    .content .review-section table td.product-details .store-name {
        font-size: 14px;
        color: #666666;
        margin-bottom: 9px;
    }

    .content .review-section table td.product-details .description {
        font-size: 14px;
        color: #333333;
        margin-bottom: 10px;
    }

    .content .review-section table td.product-details .leave-review-button {
        display: inline-block;
        padding: 8px 16px;
        font-size: 14px;
        color: #ffffff;
        background-color: #3d772d;
        text-decoration: none;
        border-radius: 5px;
        text-align: center;
    }

    .content .review-section hr {
        border: 0;
        height: 1px;
        background-color: #dddddd;
        /* Separator color */
        margin: 4px 0;
        /* Spacing around the separator */
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
                        <img src="https://api.dragonautomart.com/Email/logo.webp" alt="Company Logo">
                    </div>
                </td>
                <td width="50%" align="right">
                    <div class="text">Automotive Marketplace</div>
                </td>
            </tr>
        </table>

        <!-- Banner Section -->
        <div class="banner">
            <img src="https://api.dragonautomart.com/Email/seller_feedback_banner.webp" alt="">
        </div>

        <!-- Content Section -->
        <div class="content">
            <h1>Leave a Seller Feedback – It Only Takes a Minute!</h1>
            <p><strong>Hello {{ $order->information[0] }},</strong></p>
            <p>We’d really appreciate it if you could take a moment to share your thoughts about your recent experience
                with the seller.</p>
            <p>Your quick feedback helps us maintain great service and build trust with future buyers.</p>
            <!-- Store Section -->
            <div class="store-section">
                <div class="store-header">
                    <div class="store-logo">
                        <img src="https://api.dragonautomart.com/ShopLogo/{{ $order->shop->logo }}"
                            alt="Store Logo" width="100px" />
                        <div class="store-logo-info">
                            <div class="name">{{ $order->shop->name }}</div>
                            <div class="sold">{{$order->shop->sold_products}} Items Sold</div>
                            @if($order->seller->average_rating > 4.5)
                            <div class="rating">★★★★★</div>
                            @elseif($order->seller->average_rating > 3.5)
                            <div class="rating">★★★★☆</div>
                            @elseif($order->seller->average_rating > 2.5)
                            <div class="rating">★★★☆☆</div>
                            @elseif($order->seller->average_rating > 1.5)
                            <div class="rating">★★☆☆☆</div>
                            @elseif($order->seller->average_rating >= 1)
                            <div class="rating">★☆☆☆☆</div>
                            @else

                            @endif
                        </div>
                    </div>
                    <div class="feedback-button">
                        <a href="https://dragonautomart.com/orders/detail?id={{$order->id}}">Leave a Feedback</a>
                    </div>
                </div>
            </div>
            <!-- Product Review Section -->
            <div class="review-container">
                <div class="review-section">
                    <table>
                        <!-- Product 1 -->
                        <tr>
                            <td class="product-image">
                                @if(Str::startsWith($order_detail->product_image, 'https'))
                                <img src="{{ $order_detail->product_image }}" alt="{{ $order_detail->product_name }}">
                                @else
                                <img src="{{ 'https://api.dragonautomart.com/ProductGallery/' . $order_detail->product_image }}"
                                    alt="{{ $order_detail->product_name }}">
                                @endif
                            </td>
                            <td class="product-details">
                                <div class="store-name">{{$order->shop->name}}</div>
                                <div class="description">{{ $order_detail->product_name }}</div>
                                <!-- <a href="{{ 'https://dragonautomart.com/product/' . $order_detail->product_id . '?flag=review' }}"
                                    class="leave-review-button">Write a Review</a> -->
                            </td>
                        </tr>
                    </table>

                </div>
            </div>

            <br><br>
            <p class="font-size-14">Regards,<br />Dragon Auto Mart</p>
        </div>

        <!-- Footer Section -->
        <table class="footer" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <!-- Logo on the left -->
                <td width="50%" align="left" valign="top">
                    <div class="logo">
                        <img src="https://api.dragonautomart.com/Email/logo.webp" alt="Company Logo"
                            style="width: 150px; height: auto;">
                    </div>
                </td>
                <!-- Social icons and text on the right -->
                <td width="50%" align="right" valign="top">
                    <div class="social-section" style="padding-top: 20px;">
                        <div class="heading" style="text-align: right; margin-bottom: 10px;">Find us on social media
                            platforms</div>
                        <div class="social-icons" style="text-align: right; white-space: nowrap;">
                            <a href="https://web.facebook.com/dragonautomart?_rdc=1&_rdr"><img
                                    src="{{asset('Email/footerfacebook.webp')}}" alt="" width="30px"
                                    style="display: inline-block; margin-right: 10px;"></a>
                            <a href="https://www.instagram.com/dragonautomart/"><img
                                    src="{{asset('Email/footerinsta.webp')}}" alt="" width="30px"
                                    style="display: inline-block; margin-right: 10px;"></a>
                            <a href="https://tiktok.com/@dragonautomart"><img src="{{asset('Email/footertiktok.webp')}}"
                                    alt="" width="30px" style="display: inline-block; margin-right: 10px;"></a>
                            <a href="https://www.X.com/dragonautomart"><img src="{{asset('Email/footerx.webp')}}" alt=""
                                    width="30px" style="display: inline-block; margin-right: 10px;"></a>
                            <a href="https://www.youtube.com/@dragonautomart"><img
                                    src="{{asset('Email/footeryt.webp')}}" alt="" width="30px"
                                    style="display: inline-block;"></a>
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