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

@php
    use Illuminate\Support\Str;
@endphp

<body>
    <div class="container">
        <div class="logo">
            <img src="https://api.dragonautomart.com/gmail_banner.png" alt="Company Logo">
        </div>

        <div class="content">
        <p>Dear Customer,</p>

            <p>We hope you’re enjoying your purchase of <strong>{{ $order_detail->product_name }}</strong>!</p>

            <p>
            @if(Str::startsWith($order_detail->product_image, 'https'))
                <img src="{{ $order_detail->product_image }}" alt="{{ $order_detail->product_name }}" style="max-width: 200px; height: auto; border-radius: 5px;">
            @else
                <img src="{{ 'https://api.dragonautomart.com/ProductGallery/' . $order_detail->product_image }}" alt="{{ $order_detail->product_name }}" style="max-width: 200px; height: auto; border-radius: 5px;">
            @endif

            </p>

            <p>We’d love to hear your thoughts. Please take a moment to leave a review:</p>

            <p>
                <a href="{{ 'https://dragonautomart.com/orders/detail?id=' . $order_detail->order_id }}" 
                style="background: #007bff; color: #fff; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
                    Leave a Review
                </a>
            </p>

            <p>Thank you for shopping with Dragon Auto Mart!</p>
        </div>
    </div>

</body>

</html>
