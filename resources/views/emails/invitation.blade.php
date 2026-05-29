<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: Arial, sans-serif; font-size: 15px; color: #333; line-height: 1.6; background: #f9f9f9; margin: 0; padding: 0; }
  .wrapper { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; padding: 32px 40px; border: 1px solid #e5e7eb; }
  p { margin: 0 0 14px; }
  a { color: #1A1D2E; }
  .btn-row { margin: 28px 0 12px; }
  .btn { display: inline-block; padding: 13px 28px; border-radius: 999px; font-size: 15px; font-weight: 700; text-decoration: none; background: #1A1D2E; color: #ffffff !important; letter-spacing: 0.04em; }
  .note { font-size: 13px; color: #6b7280; margin-top: 16px; }
  .expires { font-size: 13px; color: #9ca3af; margin-top: 8px; }
  .divider { border: none; border-top: 1px solid #e5e7eb; margin: 24px 0; }
</style>
</head>
<body>
<div class="wrapper">

    <p>Hi{{ $recipientName ? ' ' . e($recipientName) : '' }},</p>

    <p>You've been invited to create your account with <strong>{{ e($schoolName) }}</strong>.</p>

    <p>Click the button below to set up your profile and add your dog(s). It only takes a few minutes — once you're done, your instructor will allocate you to a class.</p>

    <div class="btn-row">
        <a href="{{ $signUpUrl }}" class="btn">Create My Account →</a>
    </div>

    <p class="expires">This link expires in 14 days.</p>

    <hr class="divider">

    <p class="note">If you weren't expecting this invitation or believe it was sent in error, you can safely ignore this email.</p>
    <p class="note">Having trouble with the button? Copy and paste this link into your browser:<br>
        <a href="{{ $signUpUrl }}" style="color:#3569BF;word-break:break-all;">{{ $signUpUrl }}</a>
    </p>

</div>
</body>
</html>
