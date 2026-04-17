<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>E&B Laundry Hub — Notification</title>
  <style>
    body{font-family:'Poppins',Arial,sans-serif;background:#f8f9fa;color:#333;margin:0;padding:0}
    .email-wrapper{max-width:600px;margin:40px auto;background:#fff;border-radius:16px;box-shadow:0 4px 12px rgba(0,0,0,.08);overflow:hidden}
    .header{background:#173F7B;color:#fff;text-align:center;padding:24px}
    .header img{width:70px;margin-bottom:12px}
    .content{padding:32px 28px;line-height:1.7}
    h2{color:#173F7B;margin-bottom:8px}
    .details{background:#f0f4f9;border-radius:10px;padding:16px;margin-top:20px}
    .details p{margin:6px 0}
    .footer{background:#f9f9f9;text-align:center;padding:16px;font-size:13px;color:#666;border-top:1px solid #eee}
    .btn{display:inline-block;background:#173F7B;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;margin-top:20px}
    .btn:hover{background:#112f5a}
    .highlight{color:#FFB5FE;font-weight:600}
  </style>
</head>
<body>
  <div class="email-wrapper">
    <div class="header">
      <img src="{{ $logoCid ?? asset('images/E&B_Logo.png') }}" alt="E&B Laundry Logo" width="70">
      <h1>E&B Laundry Hub</h1>
      <p>Your trusted laundry partner 💧</p>
    </div>

    <div class="content">
      {{-- 👇 Body-only partial name is passed from the Mailable/Notification --}}
      @isset($bodyPartial)
        @include($bodyPartial)
      @else
        {{-- Fallback so it never blows up if $bodyPartial is missing --}}
        <p>Hello {{ $customerName ?? 'Customer' }},</p>
        <p>We have an update regarding your order.</p>
        @isset($actionUrl)
          <p><a class="btn" href="{{ $actionUrl }}">View details</a></p>
        @endisset
      @endisset
    </div>

    <div class="footer">
      <p>&copy; {{ date('Y') }} E&amp;B Laundry Hub. All rights reserved.<br>Davao City, Philippines</p>
    </div>
  </div>
</body>
</html>
