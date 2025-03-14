<!DOCTYPE html>
<html>

<head>
    <title>New User Registered</title>
</head>

<body>
    <h1>Your verification code.</h1>
    <p>Name: {{ $model->name }}</p>
    <p>Email Otp : {{ $model->otp_email }}</p>
    <p>Link : {{ url("/user/confirm-email/" . $model->activation_key) }}</p>
</body>
</html>