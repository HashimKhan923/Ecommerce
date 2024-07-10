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
            <h2>Hello {{ $buyer_name }},</h2>

            <p>{{ $body }}</p>




            <br><br>
            <p class="font-size-14">Regards,<br />Dragon Auto Mart Team</p>


            <hr>

            <p><strong>Order ID:</strong> {{ $order->id }}</p>
            <p><strong>Shop Name:</strong> {{ $shop->name }}</p>


            <table cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td width="40%" align="left" bgcolor="#eeeeee"
                        style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px;">
                        Product
                    </td>
                    <td width="40%" align="left" bgcolor="#eeeeee"
                        style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px;">
                        Name
                    </td>
                    <td width="20%" align="left" bgcolor="#eeeeee"
                        style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px;">
                        Quantity
                    </td>

                    <td width="20%" align="left" bgcolor="#eeeeee"
                        style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px;">
                        Price
                    </td>
                </tr>


                @foreach ($order->order_detail as $detail)
                    <tr>
                        <td
                            style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding: 5px 10px;">
                            <img src="{{ 'https://api.dragonautomart.com/ProductGallery/' . $detail->products->product_gallery->first()->image }}"
                                width="100px" alt="{{ $detail->products->name }}">

                        </td>
                        <td
                            style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding: 5px 10px;">
                            {{ $detail->products->name }}

                        </td>
                        <td align="center"
                            style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding: 5px 10px;">
                            {{ $detail->quantity }}
                        </td>
                        <td width="20%" align="center"
                            style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding: 5px 10px;">
                            ${{ $detail->product_price }}
                        </td>
                    </tr>
                @endforeach


            </table>

            <table cellspacing="" cellpadding="0" border="0" width="100%">
                <tr>
                    <td width="75%" align="left"
                        style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px; border-top: 3px solid #eeeeee; border-bottom: 3px solid #eeeeee;">
                        TOTAL
                    </td>
                    <td width="25%" align="right"
                        style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px; border-top: 3px solid #eeeeee; border-bottom: 3px solid #eeeeee;">
                        ${{ $order->amount }}
                    </td>
                </tr>
            </table>

        </div>
    </div>

</body>

</html>
