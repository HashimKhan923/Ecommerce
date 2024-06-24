<!DOCTYPE html>
<html>
<head>
    <title>An error occurred</title>
</head>
<body>
    <h1>An error occurred on the website</h1>
    <p><strong>Message:</strong> {{ $exceptionMessage }}</p>
    <p><strong>File:</strong> {{ $exceptionFile }}</p>
    <p><strong>Line:</strong> {{ $exceptionLine }}</p>
</body>
</html>
