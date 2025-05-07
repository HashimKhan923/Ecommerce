<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
        background-color: #4d4d4d;
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

    /* Store Section Styles */
    .store-section {
        padding: 20px;
        background-color: #f2f4f6;
        border: 1px solid #333333;
        border-radius: 8px;
    }

    .store-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .store-logo {
        flex: 1;
        display: flex;
        align-items: center;
    }

    .store-logo img {
        max-width: 80px;
        height: auto;
    }

    .store-logo-info {
        margin-left: 10px;
    }

    .store-logo-info .name {
        font-size: 19px;
        font-weight: bold;
        color: #333;
        margin-bottom: 3px;
    }

    .store-logo-info .sold {
        font-size: 14px;
        color: #666;
    }

    .store-logo-info .rating {
        font-size: 18px;
        color: #ffaa00;
        margin-top: -3px;
    }

    .feedback-button {
        flex: 1;
        text-align: right;
    }

    .feedback-button a {
        background-color: #3d772d;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        font-size: 14px;
        white-space: nowrap;
    }

    .store-section p {
        font-size: 14px;
        color: #666666;
        margin-top: 10px;
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
                        <img src="https://skartistic.com/wp-content/uploads/2025/03/Screenshot-2025-03-04-015736.png"
                            alt="Company Logo" />
                    </div>
                </td>
                <td width="50%" align="right">
                    <div class="text">Automotive Marketplace</div>
                </td>
            </tr>
        </table>

        <!-- Banner Section -->
        <div class="banner">
            <img src="https://skartistic.com/wp-content/uploads/2025/05/seller-feedback.png" alt="Welcome Banner" />
        </div>

        <!-- Content Section -->
        <div class="content">
            <h1>Leave a Seller Feedback – It Only Takes a Minute!</h1>
            <p>Hello [Customer's Name],</p>
            <p>We’d really appreciate it if you could take a moment to share your thoughts about your recent experience
                with the seller.</p>
            <p>Your quick feedback helps us maintain great service and build trust with future buyers.</p>

            <!-- Store Section -->
            <div class="store-section">
                <div class="store-header">
                    <div class="store-logo">
                        <img src="https://skartistic.com/wp-content/uploads/2025/05/Screenshot_2025-05-07_203910-removebg-preview.png"
                            alt="Store Logo" />
                        <div class="store-logo-info">
                            <div class="name">Hirev Sports</div>
                            <div class="sold">101 Items Sold</div>
                            <div class="rating">★★★★☆</div>
                        </div>
                    </div>
                    <div class="feedback-button">
                        <a href="https://your-feedback-link.com">Leave a Feedback</a>
                    </div>
                </div>
            </div>

            <h2>Order #123456</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Name</th>
                        <th>Qty</th>
                        <th>Variant</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><img src="https://skartistic.com/wp-content/uploads/2025/03/d3.webp" alt="Product Image" />
                        </td>
                        <td>HRS 2009-24 Nissan GT-R R35 LED Tail Lights - The Elite Series</td>
                        <td>2</td>
                        <td>DRL</td>
                        <td>$25.00</td>
                    </tr>
                    <tr>
                        <td><img src="https://skartistic.com/wp-content/uploads/2025/03/d2.webp" alt="Product Image" />
                        </td>
                        <td>HRS 2022-25 Toyota GR86 - Subaru BRZ LED Tail Lights - The Elite Series - RGB</td>
                        <td>1</td>
                        <td>GWQ</td>
                        <td>$30.00</td>
                    </tr>
                    <tr>
                        <td><img src="https://skartistic.com/wp-content/uploads/2025/03/d1.jpg" alt="Product Image" />
                        </td>
                        <td>HRS 2014-21 Toyota Tundra LED Tail Lights - The Elite Series</td>
                        <td>1</td>
                        <td>CBR</td>
                        <td>$15.00</td>
                    </tr>
                </tbody>
            </table>

            <!-- Totals -->
            <div class="totals">
                <table>
                    <tr>
                        <td><strong>Tax:</strong></td>
                        <td><span>$5.00</span></td>
                    </tr>
                    <tr>
                        <td><strong>Signature:</strong></td>
                        <td><span>$5.00</span></td>
                    </tr>
                    <tr>
                        <td><strong>Insurance:</strong></td>
                        <td><span>$5.00</span></td>
                    </tr>
                    <tr>
                        <td><strong>Shipping:</strong></td>
                        <td><span>$5.00</span></td>
                    </tr>
                    <tr>
                        <td><strong>Discount:</strong></td>
                        <td><span>-$2.00</span></td>
                    </tr>
                    <tr>
                        <td><strong>Total:</strong></td>
                        <td><span>$77.00</span></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Footer Section -->
        <table class="footer" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td width="50%" align="left" valign="top">
                    <div class="logo">
                        <img src="https://skartistic.com/wp-content/uploads/2025/03/Screenshot-2025-03-04-015736.png"
                            alt="Company Logo" style="width: 150px; height: auto;" />
                    </div>
                </td>
                <td width="50%" align="right" valign="top">
                    <div class="social-section" style="padding-top: 20px;">
                        <div class="heading">Find us on social media platforms</div>
                        <div class="social-icons">
                            <img src="https://via.placeholder.com/24" alt="" />
                            <img src="https://via.placeholder.com/24" alt="" />
                            <img src="https://via.placeholder.com/24" alt="" />
                            <img src="https://via.placeholder.com/24" alt="" />
                            <img src="https://via.placeholder.com/24" alt="" />
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