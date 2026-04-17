{{-- resources/views/emails/password_change_notification.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Password Updated • E&B Laundry Hub</title>
</head>

<body style="margin:0; padding:0; background-color:#f5f5f5; font-family: 'Poppins', Arial, sans-serif;">

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
    style="background-color:#f5f5f5; padding:24px 0;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
          style="max-width:520px; background-color:#ffffff; border-radius:16px; overflow:hidden;
                          box-shadow:0 8px 24px rgba(0,0,0,0.08);">
          {{-- Header / Logo --}}
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
                Your password was updated
              </h1>
            </td>
          </tr>

          {{-- Main copy --}}
          <tr>
            <td style="padding:14px 24px 0; text-align:left;">
              <p style="font-size:14px; line-height:1.6; margin:0; color:#555555;">
                Hello {{ $userName ?? 'Valued Customer' }},
              </p>
              <p style="font-size:14px; line-height:1.6; margin:10px 0 0; color:#555555;">
                This is a confirmation that the password for your
                <strong>E&amp;B Laundry Hub</strong> account was successfully changed
                @if(!empty($changedAt))
                on <strong>{{ $changedAt }}</strong>
                @endif
                .
              </p>
              <p style="font-size:14px; line-height:1.6; margin:10px 0 0; color:#555555;">
                If you made this change, no further action is needed.
              </p>
            </td>
          </tr>

          {{-- Security warning --}}
          <tr>
            <td style="padding:14px 24px 10px; text-align:left;">
              <p style="font-size:13px; line-height:1.6; margin:0; color:#e63946;">
                Didn’t change your password?
              </p>
              <p style="font-size:13px; line-height:1.6; margin:4px 0 0; color:#777777;">
                If you did <strong>not</strong> request this change, we recommend that you:
              </p>
              <ul style="font-size:13px; line-height:1.6; margin:6px 0 0 18px; padding:0; color:#777777;">
                <li>Reset your password again from the login page.</li>
                <li>Contact us immediately at
                  <strong>{{ $supportEmail ?? 'support@example.com' }}</strong>.
                </li>
              </ul>
            </td>
          </tr>

          {{-- Footer --}}
          <tr>
            <td style="padding:14px 24px 22px; text-align:left;">
              <p style="font-size:12px; line-height:1.5; margin:0; color:#aaaaaa;">
                This email was sent automatically by E&amp;B Laundry Hub. Please do not reply
                directly to this message.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>

</html>