<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation Email</title>
</head>
<body>
    <h1>This is Confirmation Email</h1>
    <h2>Kepada {{$user->username}}</h2>
    <h3>Kode OTP Anda adalah {{$otp_code}}</h3>
</body>
</html>