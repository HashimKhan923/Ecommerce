<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            width: 100%;
            height: auto;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            display: block;
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
        <div class="logo">
            <img src="https://api.dragonautomart.com/gmail_banner.png" alt="Company Logo">
        </div>

        <div class="content">
        <h2>Good News!</h2>
    
        <p>We are excited to inform you that the product you were interested in is now back in stock:</p>
        <br>
        <img src="{{ 'https://api.dragonautomart.com/ProductGallery/' . $productImage }}"
        width="100px" alt="">
        <h3>{{ $productName }}</h3>
        @if($variantName)
            <p>Variant: {{ $variantName }}</p>
        @endif

        <p>Don’t miss out! Click the link below to view the product and make your purchase while it's still available:</p>
        
        <a href="{{ $productUrl }}" style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 5px; font-size: 16px;">View Product</a>

        <p>If you have any questions, feel free to reach out to our support team at support@dragonautomart.com.</p>

        <p>Thank you,<br>The Dragon Auto Mart Team</p>
        </div>
    </div>
</body>

</html>
