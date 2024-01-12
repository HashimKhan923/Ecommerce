<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Query Email</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
    }

    .container {
      max-width: 600px;
      margin: 0 auto;
    }

    img {
      max-width: 100%;
      height: auto;
    }

    .btn {
      display: inline-block;
      font-weight: 400;
      color: #212529;
      text-align: center;
      vertical-align: middle;
      cursor: pointer;
      user-select: none;
      border: 1px solid transparent;
      padding: 0.375rem 0.75rem;
      font-size: 1rem;
      line-height: 1.5;
      border-radius: 0.25rem;
      transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .btn-primary {
      color: #fff;
      background-color: #007bff;
      border-color: #007bff;
    }

    .btn-primary:hover {
      color: #fff;
      background-color: #0056b3;
      border-color: #0056b3;
    }
  </style>
</head>
<body>

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

    <p>Please respond to the customer's query as soon as possible.</p>

    <p>Thank you,</p>
    <p>Dragon Auto Mart</p>

    <p>
      <a href="https://dragonautomart.com" class="btn btn-primary">Visit Our Website</a>
    </p>
  </div>

</body>
</html>
