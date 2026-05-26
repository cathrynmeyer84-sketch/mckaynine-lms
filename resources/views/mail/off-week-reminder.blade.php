<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  body { margin:0; padding:0; background:#f5f1ea; font-family:'Figtree',Arial,sans-serif; }
  .wrap { max-width:600px; margin:32px auto; background:#fff; border-radius:16px; overflow:hidden; }
  .header { background:#001d6d; padding:28px 32px; }
  .header-logo { display:flex; align-items:center; gap:10px; }
  .logo-icon { width:36px; height:36px; background:#f59e0b; border-radius:8px; display:inline-block; }
  .logo-text { color:#fff; font-size:16px; font-weight:600; }
  .body { padding:32px; }
  h1 { color:#001d6d; font-size:22px; font-weight:700; margin:0 0 16px; }
  p { color:#374151; font-size:15px; line-height:1.65; margin:0 0 14px; }
  .info-card { background:#f5f1ea; border-radius:12px; padding:20px; margin:20px 0; }
  .info-card p { margin:0 0 6px; font-size:14px; }
  .info-card strong { color:#001d6d; }
  .footer { background:#f5f1ea; padding:20px 32px; text-align:center; }
  .footer p { color:#9ca3af; font-size:12px; margin:0; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <div class="header-logo">
      <div class="logo-icon"></div>
      <span class="logo-text">McKaynine Dog School</span>
    </div>
  </div>
  <div class="body">
    <h1>No class this week 🐾</h1>
    @foreach(explode("\n", $bodyText) as $line)
      @if(trim($line))
        <p>{{ $line }}</p>
      @endif
    @endforeach

    <div class="info-card">
      <p><strong>Class:</strong> {{ $className }}</p>
      <p><strong>Cancelled:</strong> {{ $offDate }}</p>
      <p><strong>Reason:</strong> {{ $offReason }}</p>
      <p style="margin:0"><strong>Next class:</strong> {{ $nextClassDate }}</p>
    </div>

    <p style="color:#9ca3af; font-size:13px;">If you have any questions, feel free to reply to this email.</p>
  </div>
  <div class="footer">
    <p>McKaynine Dog School &middot; This is an automated reminder</p>
  </div>
</div>
</body>
</html>
