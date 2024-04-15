<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Completed</title>
    <style>
        /* Your styles here */
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <div class="logo">
                    <img src="https://api.dragonautomart.com/emailLogo.png" width="200" class="img-responsive"
                        alt="Company Logo">
                    <div class=" text-left" style="margin-top:10px">
                        <h3>Dragon Auto Mart</h3>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="content">
            <p>Hi {{ $buyer_name }},</p>
            <p>{{ $body }}</p>
            <hr>
            <h3>Order Number: {{ $order->id }}</h3>
            <br>
            <table cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td width="40%" align="left" bgcolor="#eeeeee">
                        Product
                    </td>
                    <td width="40%" align="left" bgcolor="#eeeeee">
                        Name
                    </td>
                    <td width="20%" align="left" bgcolor="#eeeeee">
                        Quantity
                    </td>
                    <td width="20%" align="left" bgcolor="#eeeeee">
                        Price
                    </td>
                </tr>
                @foreach($order->order_detail as $detail)
                <tr>
                    <td>
                    <img src="https://api.dragonautomart.com/ProductGallery/{{ $detail->products->product_gallery->first()->image }}" width="100px" alt="{{ $detail->products->name }}">

                    </td>
                    <td>
                        {{ $detail->products->name }}
                    </td>
                    <td align="center">
                        {{ $detail->quantity }}
                    </td>
                    <td align="center">
                        ${{ $detail->total_price }}
                    </td>
                </tr>
                @endforeach
            </table>
            <table cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                    <td width="75%" align="left">
                        TOTAL
                    </td>
                    <td width="25%" align="right">
                        ${{ $order->total_amount }}
                    </td>
                </tr>
            </table>
            <br>
            <p class="font-size-14">Regards,<br />Dragon Auto Mart Team</p>
        </div>
    </div>
</body>

</html>
