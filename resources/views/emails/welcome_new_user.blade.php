{{-- resources/views/emails/welcome_new_user.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Welcome to E&B Laundry Hub</title>
</head>

<body style="margin:0; padding:0; background-color:#f5f5f5; font-family: 'Poppins', Arial, sans-serif;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background-color:#f5f5f5; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:520px; background-color:#ffffff; border-radius:16px; overflow:hidden;
                              box-shadow:0 8px 24px rgba(0,0,0,0.08);">

                    {{-- Logo / Brand --}}
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

                    {{-- Title --}}
                    <tr>
                        <td style="padding:10px 24px 0; text-align:center;">
                            <h1 style="font-size:20px; margin:0; color:#333333;">
                                Welcome to E&amp;B Laundry Hub 🎉
                            </h1>
                        </td>
                    </tr>

                    {{-- Intro text --}}
                    <tr>
                        <td style="padding:12px 24px 0; text-align:left;">
                            <p style="font-size:14px; line-height:1.6; margin:0; color:#555555;">
                                Hi <strong>{{ $userName ?? 'there' }}</strong>,
                            </p>
                            <p style="font-size:14px; line-height:1.6; margin:8px 0 0; color:#555555;">
                                Thanks for creating an account with
                                <strong>E&amp;B Laundry Hub</strong>! You can now book pickups,
                                track your laundry, and view your order history all in one place.
                            </p>
                        </td>
                    </tr>

                    {{-- Button + helper text --}}
                    <tr>
                        <td align="center" style="padding:24px 24px 4px;">
                            <a href="{{ $loginUrl ?? '#' }}"
                                style="display:inline-block; padding:12px 28px; border-radius:999px;
                                      background:linear-gradient(90deg,#ff6bd9,#ff7ee8);
                                      color:#ffffff; font-size:15px; font-weight:600;
                                      text-decoration:none;">
                                Go to my account
                            </a>

                            @if (!empty($loginUrl))
                                <p style="font-size:12px; word-break:break-all; margin:8px 0 0; color:#777777;">
                                    Or copy and paste this link into your browser:
                                    <br>
                                    <span>{{ $loginUrl }}</span>
                                </p>
                            @endif
                        </td>
                    </tr>

                    {{-- Extra information --}}
                    <tr>
                        <td style="padding:12px 24px 18px; text-align:left;">
                            <p style="font-size:13px; line-height:1.6; margin:0; color:#666666;">
                                If you didn’t create this account, you can safely ignore this email.
                                If you have any questions, just reply to this message or contact us at
                                <strong>{{ $supportEmail ?? 'support@example.com' }}</strong>.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:10px 24px 22px; text-align:center;">
                            <p style="font-size:11px; line-height:1.4; margin:0; color:#aaaaaa;">
                                &copy; {{ date('Y') }} E&amp;B Laundry Hub. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>
