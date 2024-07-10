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
            <p class="font-size-18">Hello,. {{ $Seller->name }}</p>

            <hr>

            <div>
                <img src="{{ 'https://api.dragonautomart.com/ProductGallery/' . $ProductImage }}" width="150px"
                    alt="">

                <h4><strong>Product : </strong>{{ $ProductName }}</h4>
                <!-- <img src="https://api.dragonautomart.com/ShopLogo/{{ $ShopImage }}" width="100" alt="Product Image"> -->
                <h3><strong>Shop : </strong>{{ $ShopName }}</h3>
            </div>
            <p><strong>Customer Name:</strong>{{ $Customer->name }}</p>
            <p><strong>Customer Email:</strong>{{ $Customer->email }}</p>
            <p><strong>Message:</strong> {{ $Msg }}
            <p>
                <br>
                <a href="https://seller.dragonautomart.com/seller/messages"
                    style="display: inline-block; padding: 10px 20px; background-color: green; color: #fff; text-align: center; text-decoration: none; border: none; border-radius: 5px; cursor: pointer;">Reply</a>


                <hr>



                <br><br>
            <p class="font-size-14">Regards,<br />Dragon Auto Mart</p>
        </div>
    </div>

</body>

</html>
