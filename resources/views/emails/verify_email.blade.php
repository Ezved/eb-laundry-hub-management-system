<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Verify your E&amp;B Laundry Hub account</title>
</head>

<body>
    <p>Hi {{ $user->name }},</p>

    <p>Thank you for registering with <strong>E&amp;B Laundry Hub</strong>.</p>

    <p>Please verify your email address by clicking the button below:</p>

    <p>
        <a href="{{ $verificationUrl }}" target="_blank"
            style="display:inline-block;padding:10px 20px;background-color:#173F7B;color:#ffffff !important;text-decoration:none;border-radius:4px;font-weight:600;">
            Verify my account
        </a>
    </p>


    <p>If you did not create this account, you can safely ignore this email.</p>
</body>

</html>
