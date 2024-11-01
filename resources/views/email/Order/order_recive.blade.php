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
            <p>Hi {{ $buyer_name }},</p>
            <p>Thank you for placing your order. We are currently preparing it for shipment.</p>
            <p>Please allow 3-5 business days for processing and shipping due to high demand.</p>
            <p>For pre-order products, please allow 24-48 hours after the restock date mentioned in the product
                description for preparation.</p>
            <p>You will receive an email with tracking information once your order has been shipped.</p>
            <p>If your shipping address is at an apartment complex or condo, your order will be sent to the nearest
                FedEx location. We appreciate your understanding and patience.</p>


            <br><br>
            <p class="font-size-14">Regards,<br />Dragon Auto Mart Team</p>


            <hr>

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
                        Varient
                    </td>

                    <td width="20%" align="left" bgcolor="#eeeeee"
                        style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px;">
                        Price
                    </td>
                </tr>

               
                @foreach ($productsByVendor as $vendorId => $vendorProducts)
                    @foreach ($vendorProducts as $product)
                        <?php
                        $orderProduct = collect($request->products)
                            ->where('product_id', $product->id)
                            ->first();
                        
                        $price = $orderProduct['product_price'] * $orderProduct['quantity'];
                        
                        ?>

                        <tr>
                            <td
                                style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding: 5px 10px;">
                                <img src="{{ 'https://api.dragonautomart.com/ProductGallery/' . $orderProduct['product_image'] }}"
                                    width="100px" alt="">
                            </td>
                            <td
                                style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding: 5px 10px;">
                                {{ $product->name }}

                            </td>
                            <td align="center"
                                style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding: 5px 10px;">
                                {{ $orderProduct['quantity'] }}
                            </td>
                            <td align="center"
                                style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding: 5px 10px;">
                                @if(isset($orderProduct['product_varient']))
                                {{ $orderProduct['product_varient'] }}
                                @endif
                            </td>
                            <td width="20%" align="center"
                                style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding: 5px 10px;">
                                ${{ $price }}
                            </td>
                        </tr>
                    @endforeach
                @endforeach

            </table>

            <table cellspacing="" cellpadding="0" border="0" width="100%">
                @if ($TotalShippingAmount)
                    <tr>
                        <td width="75%" align="left"
                            style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 12px; font-weight: 600; line-height: 24px; padding: 10px; border-top: 3px solid #eeeeee; border-bottom: 3px solid #eeeeee;">
                            Shipping Charges
                        </td>
                        <td width="25%" align="right"
                            style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 12px; font-weight: 600; line-height: 24px; padding: 10px; border-top: 3px solid #eeeeee; border-bottom: 3px solid #eeeeee;">
                            ${{ $TotalShippingAmount }}
                        </td>
                    </tr>
                @endif
                @if ($request->tax)
                    <tr>
                        <td width="75%" align="left"
                            style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 12px; font-weight: 600; line-height: 24px; padding: 10px; border-top: 3px solid #eeeeee; border-bottom: 3px solid #eeeeee;">
                            Tax({{ floatval($request->tax[2]) }}%)
                        </td>
                        <td width="25%" align="right"
                            style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 12px; font-weight: 600; line-height: 24px; padding: 10px; border-top: 3px solid #eeeeee; border-bottom: 3px solid #eeeeee;">
                            ${{ floatval($request->tax[0]) }}
                        </td>
                    </tr>
                @endif
                @if ($request->insurance)
                    <tr>
                        <td width="75%" align="left"
                            style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 12px; font-weight: 600; line-height: 24px; padding: 10px; border-top: 3px solid #eeeeee; border-bottom: 3px solid #eeeeee;">
                            Insurance
                        </td>
                        <td width="25%" align="right"
                            style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 12px; font-weight: 600; line-height: 24px; padding: 10px; border-top: 3px solid #eeeeee; border-bottom: 3px solid #eeeeee;">
                            ${{ floatval($request->insurance[0]) }}
                        </td>
                    </tr>
                @endif
                @if ($request->signature)
                    <tr>
                        <td width="75%" align="left"
                            style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 12px; font-weight: 600; line-height: 24px; padding: 10px; border-top: 3px solid #eeeeee; border-bottom: 3px solid #eeeeee;">
                            Signature
                        </td>
                        <td width="25%" align="right"
                            style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 12px; font-weight: 600; line-height: 24px; padding: 10px; border-top: 3px solid #eeeeee; border-bottom: 3px solid #eeeeee;">
                            ${{ floatval($request->signature[0]) }}
                        </td>
                    </tr>
                @endif
                @if ($request->coupon_discount)
                    <tr>
                        <td width="75%" align="left"
                            style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 12px; font-weight: 600; line-height: 24px; padding: 10px; border-top: 3px solid #eeeeee; border-bottom: 3px solid #eeeeee;">
                            Coupon Discount
                        </td>
                        <td width="25%" align="right"
                            style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 12px; font-weight: 600; line-height: 24px; padding: 10px; border-top: 3px solid #eeeeee; border-bottom: 3px solid #eeeeee;">
                            ${{ $request->coupon_discount }}
                        </td>
                    </tr>
                @endif
                <tr>
                    <td width="75%" align="left"
                        style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px; border-top: 3px solid #eeeeee; border-bottom: 3px solid #eeeeee;">
                        TOTAL
                    </td>
                    <td width="25%" align="right"
                        style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 800; line-height: 24px; padding: 10px; border-top: 3px solid #eeeeee; border-bottom: 3px solid #eeeeee;">
                        ${{ number_format($request->amount, 2) }}

                    </td>
                </tr>
            </table>
            <br>

            <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%"
                style="max-width:300px;">
                <tr>
                    <td align="left" valign="top"
                        style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px;">
                        <p style="font-weight: 800;">Delivery Address</p>
                        <p>{{ $request->information[0] }},<br>{{ $request->information[1] }},<br>{{ $request->information[2] }},
                            {{ $request->information[3] }},
                            {{ $request->information[5] }},<br>{{ $request->information[4] }},<br>{{ $request->information[6] }},<br>{{ $request->information[7] }}
                        </p>

                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>

</html>
