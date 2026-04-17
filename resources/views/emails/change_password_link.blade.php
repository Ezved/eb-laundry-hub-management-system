{{-- resources/views/emails/change_password_link.blade.php --}}
{{-- added 11/16/2025 --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Change your password • E&B Laundry Hub</title>
</head>

<body style="margin:0; padding:0; background-color:#f5f5f5; font-family: 'Poppins', Arial, sans-serif;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background-color:#f5f5f5; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:520px; background-color:#ffffff; border-radius:16px; overflow:hidden;
                          box-shadow:0 8px 24px rgba(0,0,0,0.08);">
                    <tr>
                        <td align="center" style="padding:24px 24px 8px;">
                            <div style="font-size:30px; letter-spacing:4px; font-weight:800; color:#2e2a60;">
                                E&amp;B
                            </div>
                            <div style="font-size:16px; font-weight:600; color:#ff4fb9; margin-top:2px;">
                                Laundry Hub
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:10px 24px 0; text-align:center;">
                            <h1 style="font-size:20px; margin:0; color:#333333;">Change your password</h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:10px 24px 0; text-align:center;">
                            <p style="font-size:14px; line-height:1.5; margin:0; color:#555555;">
                                We received a request to change the password for your
                                <strong>E&amp;B Laundry Hub</strong> account.
                                Click the button below to set a new password.
                            </p>
                            <p style="font-size:14px; line-height:1.5; margin:0; color:#555555;">
                                This link will expire in 10 minutes.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:24px 24px 16px;">
                            {{-- Button uses the public reset URL --}}
                            <a href="{{ $resetUrl }}"
                                style="display:inline-block; padding:12px 28px; border-radius:999px;
          background:linear-gradient(90deg,#ff6bd9,#ff7ee8);
          color:#ffffff; font-size:15px; font-weight:600;
          text-decoration:none;">
                                Change Password
                            </a>

                            <p style="font-size:12px; word-break:break-all; margin:6px 0 0; color:#777777;">
                                {{ $resetUrl }}
                            </p>

                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 24px 18px; text-align:left;">
                            <p style="font-size:12px; line-height:1.5; margin:0; color:#777777;">
                                If the button doesn’t work, copy and paste this link into your browser:
                            </p>
                            <p style="font-size:12px; word-break:break-all; margin:6px 0 0; color:#777777;">
                                {{ $resetUrl }}
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:16px 24px 22px; text-align:left;">
                            <p style="font-size:12px; line-height:1.5; margin:0; color:#aaaaaa;">
                                If you didn’t request a password change, you can safely ignore this email.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>
