<!DOCTYPE html>
<html>

<head>
    <title>Contact Us Information</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <style type="text/css">
        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            -ms-interpolation-mode: bicubic;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        table {
            border-collapse: collapse !important;
        }

        body {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        @media screen and (max-width: 480px) {
            .mobile-hide {
                display: none !important;
            }

            .mobile-center {
                text-align: center !important;
            }
        }

        div[style*="margin: 16px 0;"] {
            margin: 0 !important;
        }
    </style>
</head>

<body style="margin: 0 !important; padding: 0 !important; background-color: #eeeeee;" bgcolor="#eeeeee">

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="background-color: #eeeeee;" bgcolor="#eeeeee">

                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px;">
                    <tr>
                        <td align="center" valign="top" style="font-size:0; padding: 6px;padding-top: 43px;" bgcolor="#4D4D4D">

                        <div style="display:inline-block; max-width:100%; min-width:100px; vertical-align:top; width:100%;">
                                <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:300px;">
                                    <tr>
                                        <td align="left" valign="top" style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 28px; font-weight: 600; line-height: 48px;" class="mobile-center">
                                            <h1 style="font-size: 32px; font-weight: 500; margin: 0; color: #ffffff; margin-left:24px">Dragon Auto Mart</h1>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div style="display:inline-block; max-width:100%; min-width:100px; vertical-align:top; width:100%;" class="mobile-hide">
                                <table align="right" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:300px;">
                                    <tr>
                                        <td align="right" valign="top" style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400;">
                                            <!-- <img src="https://development.dragonautomart.com/assets/logowhite-15c29e7e.webp" width="100px" style="margin-top: -73px; margin-right:24px"alt=""> -->
                                        </td>
                                    </tr>
                                </table>
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding: 35px 35px 20px 35px; background-color: #ffffff;" bgcolor="#ffffff">
                            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px;">
                                <tr>
                                    <td align="left" style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; padding-top: 10px;">
                                    <div class="container">
    <h2>Customer Query about Product</h2>
    <p>Hello {{$Seller->name}},</p>

    <p>The following customer has sent a query about a product:</p>

    <div>
      <img src="https://api.dragonautomart.com/ProductGallery/{{$ProductImage}}" width="150" alt="Product Image">
      <h3>{{$ProductName}}</h3>
    </div>

    <p><strong>Customer Information:</strong></p>
    <p><strong>Name:</strong>{{$Customer->name}}</p>
    <p><strong>Email:</strong>{{$Customer->email}}</p>
    <p><strong>Query:</strong></p>
    <p>{{$Msg}}</p>

    <hr>

    <p>Please respond to the customer's query as soon as possible.</p>

    <p>Thank you,</p>
    <p>Dragon Auto Mart</p>

    <p>
      <a href="https://dragonautomart.com" class="btn btn-primary">Visit Our Website</a>
    </p>
  </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>