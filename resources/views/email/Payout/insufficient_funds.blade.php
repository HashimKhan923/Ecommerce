<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payout Failed - Insufficient Funds</title>
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

        .alert {
            background-color: #fdecea;
            color: #611a15;
            padding: 12px;
            border-radius: 5px;
            margin: 15px 0;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <img src="https://api.dragonautomart.com/gmail_banner.png" alt="Company Logo">
        </div>

        <div class="content">
            <p class="font-size-18">Hello,</p>

            <p class="card-text">
                A seller payout could not be processed due to insufficient funds in the Stripe account.
            </p>

            <div class="alert">
                <strong>Stripe Error:</strong><br>
                {{ $error }}
            </div>

            <p class="card-text">
                <strong>Payout ID:</strong> {{ $payout_id }}<br>
                <strong>Seller Name:</strong> {{ $seller_name }}<br>
                <strong>Seller Email:</strong> {{ $seller_email }}<br>
                <strong>Payout Amount:</strong> ${{ $amount }}
            </p>

            <p class="card-text">
                Please add funds to the Stripe balance and re-run the payout process.
            </p>

            <br><br>
            <p class="font-size-14">
                Regards,<br />
                Dragon Auto Mart
            </p>
        </div>
    </div>
</body>

</html>
