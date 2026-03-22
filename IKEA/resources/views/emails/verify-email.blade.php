<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify your email — IKEA Philippines</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background: #f5f5f0; color: #111; padding: 32px 16px; }
        .wrapper { max-width: 600px; margin: 0 auto; }

        .email-header { background: #0058A3; border-radius: 10px 10px 0 0; padding: 32px; text-align: center; }
        .logo-box { display: inline-block; background: #FFDB00; color: #0058A3; font-weight: 900; font-size: 28px; letter-spacing: 2px; padding: 6px 18px; border-radius: 4px; margin-bottom: 20px; }
        .header-icon { font-size: 48px; display: block; margin-bottom: 12px; }
        .email-header h1 { color: white; font-size: 24px; font-weight: 800; }
        .email-header p  { color: rgba(255,255,255,.85); font-size: 15px; margin-top: 8px; }

        .email-body { background: white; padding: 32px; }

        .message-box { background: #e3f2fd; color: #1565c0; border-left: 4px solid #0058A3; border-radius: 8px; padding: 16px 20px; margin-bottom: 28px; font-size: 14px; line-height: 1.7; }

        .cta-wrap { text-align: center; margin: 32px 0; }
        .cta-btn  { display: inline-block; background: #FFDB00; color: #111; font-weight: 800; font-size: 16px; padding: 16px 40px; border-radius: 40px; text-decoration: none; letter-spacing: .3px; }

        .url-fallback { background: #f5f5f0; border-radius: 8px; padding: 14px 18px; margin: 24px 0; word-break: break-all; }
        .url-fallback p { font-size: 12px; color: #767676; margin-bottom: 6px; }
        .url-fallback a { font-size: 12px; color: #0058A3; text-decoration: none; }

        .expiry-note { font-size: 13px; color: #767676; text-align: center; line-height: 1.6; margin-bottom: 8px; }

        .email-footer { background: #111; border-radius: 0 0 10px 10px; padding: 24px 32px; text-align: center; }
        .email-footer p { color: rgba(255,255,255,.5); font-size: 12px; line-height: 1.7; }
        .email-footer a { color: #FFDB00; text-decoration: none; }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="email-header">
        <div class="logo-box">IKEA</div>
        <span class="header-icon">✉️</span>
        <h1>Verify your email address</h1>
        <p>Hi {{ $user->name }}, one quick step before you start shopping.</p>
    </div>

    <div class="email-body">

        <div class="message-box">
            You're almost there! Click the button below to verify your email address and activate your
            IKEA Philippines account. This link will expire in <strong>60 minutes</strong>.
        </div>

        <div class="cta-wrap">
            <a href="{{ $url }}" class="cta-btn">
                Verify Email Address →
            </a>
        </div>

        <p class="expiry-note">
            If the button doesn't work, copy and paste this link into your browser:
        </p>

        <div class="url-fallback">
            <p>Verification link:</p>
            <a href="{{ $url }}">{{ $url }}</a>
        </div>

        <p style="font-size:13px;color:#767676;text-align:center;line-height:1.6;margin-top:24px;">
            If you did not create an account, no further action is required —
            simply ignore this email.
        </p>

    </div>

    <div class="email-footer">
        <p>
            © {{ date('Y') }} IKEA Philippines. All rights reserved.<br>
            <a href="{{ url('/') }}">ikea.ph</a> &nbsp;·&nbsp;
            <a href="{{ url('/shop') }}">Shop</a> &nbsp;·&nbsp;
            <a href="{{ url('/dashboard') }}">My Account</a>
        </p>
    </div>

</div>
</body>
</html>