<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>419 Session Expired</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

</head>

<body>
    <div class="container mt-5 pt-5">
        <div class="alert alert-danger text-center">
            <h2 class="display-3">419</h2>
            <p class="display-5">Oops! Session Is Expired, You Must Login To Continue The Session.</p>
            <a href="/" class="btn btn-primary">Go Back To The Homepage</a>
        </div>
    </div>
</body>

</html>