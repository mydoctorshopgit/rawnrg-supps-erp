<!DOCTYPE html>
<html>
<head>
    <title>Account Approved</title>
</head>
<body>
    <h1>Hello, {{ $name }}</h1>
    <p>Your account has been approved.</p>
    <p><strong>Email:</strong> {{ $email }}</p>
    <p><strong>Temporary Password:</strong> {{ $password }}</p>
    <p>You can log in using the following link:</p>
    <p><a href="{{ $loginUrl }}">{{ $loginUrl }}</a></p>
    <p>For security reasons, please change your password after logging in.</p>
    <br>
    <p>Best Regards,<br> {{ config('app.name') }}</p>
</body>
</html>
