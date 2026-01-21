<!DOCTYPE html>
<html>

<head>
    <title>Account Status Update</title>
</head>

<body>
    <p>Hello {{ $user->name }},</p>
    <p>Your CSMS account status has been updated.</p>

    <p>
        <strong>Status:</strong> {{ ucfirst($status) }}
    </p>

    @if ($status === 'active')
        <p>You may now log in and access the system.</p>
    @elseif($status === 'rejected')
        <p>If you believe this is an error, please contact the OLOI office.</p>
    @elseif ($status === 'disabled')
        <p>Your account has been disabled. Please contact the OLOI office for more information.</p>
    @endif
</body>

</html>
