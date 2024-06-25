<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
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
            max-width: 100%;
            height: auto;
        }

        .company-name {
            text-align: right;
            margin-top: 10px;
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
        <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
    <div class="logo">
    <img src="{{ asset('NewEmailLogo.png') }}" width="250px" alt="Company Logo" class="logo">
        <div class=" text-left"  style="margin-top:10px">
        <h3>Dragon Auto Mart</h2>
    </div>
    </div>
</div>


</div>
        <hr>
        <div class="content">
        <p>Hi {{$buyer_name}},</p>
        <p> Thank you for placing your order. We're currently preparing it for shipment.</p>
        <p> Due to high demand, please allow 3-5 business days for preparation and shipping. </p>

        <p>For pre-order products, kindly allow 24-48 hours after the restock date mentioned in the product description for preparation. </p>

<p>You'll receive an email with tracking information once your order is shipped.</p>

<p>If you have a shipping address at an apartment complex or condo, your order will be sent to the nearest FedEx location. We appreciate your understanding and patience.</p>


        <br><br>
        <p class="font-size-14">Regards,<br />Dragon Auto Mart Team</p>


<hr>

        <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td width="40%" align="left" bgcolor="#eeeeee" style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px;">
                                                    Product
                                                </td>
                                                <td width="40%" align="left" bgcolor="#eeeeee" style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px;">
                                                    Name
                                                </td>
                                                <td width="20%" align="left" bgcolor="#eeeeee" style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px;">
                                                    Quantity
                                                </td>

                                                <td width="20%" align="left" bgcolor="#eeeeee" style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px;">
                                                    Price
                                                </td>
                                            </tr>
                                            

                                            @foreach($productsByVendor as $vendorId => $vendorProducts)
                                            @foreach($vendorProducts as $product)
                                            <?php
                                            $orderProduct = collect($request->products)->where('product_id', $product->id)->first();

                                            $price = $orderProduct['product_price'] * $orderProduct['quantity'];

                                            ?>

                                            <tr>
                                                <td style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding: 5px 10px;">
                                                <img src="{{ 'https://api.dragonautomart.com/ProductGallery/' . $orderProduct['product_image'] }}" width="100px" alt="{{ $orderProduct['product_name'] }}">
                                                </td>
                                                <td  style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding: 5px 10px;">
                                                {{$product->name}}

                                                </td>
                                                <td align="center" style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding: 5px 10px;">
                                                {{ $orderProduct['quantity'] }}
                                                </td>
                                                <td width="20%" align="center" style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding: 5px 10px;">
                                                ${{$price}}
                                                </td>
                                            </tr>


                                        
                            @endforeach
                            @endforeach    

                                        </table>

                                        <table cellspacing="" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td width="75%" align="left" style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px; border-top: 3px solid #eeeeee; border-bottom: 3px solid #eeeeee;">
                                                    TOTAL
                                                </td>
                                                <td width="25%" align="right" style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px; border-top: 3px solid #eeeeee; border-bottom: 3px solid #eeeeee;">
                                                ${{$request->amount}}
                                                </td>
                                            </tr>
                                        </table>
                                        <br>

                                        <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:300px;">
                                                <tr>
                                                    <td align="left" valign="top" style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px;">
                                                        <p style="font-weight: 800;">Delivery Address</p>
                                                        <p>{{$request->information[0]}},<br>{{$request->information[1]}},<br>{{$request->information[2]}}, {{$request->information[3]}}, {{$request->information[5]}},<br>{{$request->information[4]}},<br>{{$request->information[6]}},<br>{{$request->information[7]}}</p>

                                                    </td>
                                                </tr>
                                            </table>
        </div>
    </div>

</body>

</html>