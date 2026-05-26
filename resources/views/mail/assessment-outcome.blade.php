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
  .logo-icon { width:36px; height:36px; background:#f59e0b; border-radius:8px; display:flex; align-items:center; justify-content:center; }
  .logo-text { color:#fff; font-size:16px; font-weight:600; }
  .body { padding:32px; }
  h1 { color:#001d6d; font-size:22px; font-weight:700; margin:0 0 16px; }
  p { color:#374151; font-size:15px; line-height:1.65; margin:0 0 14px; }
  .outcome-badge { display:inline-block; padding:6px 16px; border-radius:99px; font-size:13px; font-weight:600; margin-bottom:20px; }
  .badge-group { background:#dcfce7; color:#166534; }
  .badge-private { background:#fef3c7; color:#92400e; }
  .badge-behaviourist { background:#fee2e2; color:#991b1b; }
  .class-card { background:#f5f1ea; border-radius:12px; padding:20px; margin:20px 0; }
  .class-card h3 { color:#001d6d; font-size:16px; font-weight:600; margin:0 0 8px; }
  .btn { display:inline-block; background:#001d6d; color:#fff !important; text-decoration:none; padding:12px 28px; border-radius:10px; font-weight:600; font-size:14px; margin:4px 4px 4px 0; }
  .btn-amber { background:#f59e0b; }
  .footer { background:#f9fafb; padding:20px 32px; border-top:1px solid #e5e7eb; }
  .footer p { color:#9ca3af; font-size:12px; margin:0; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <div class="header-logo">
      <div class="logo-icon">
        <svg width="20" height="20" fill="white" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
      </div>
      <span class="logo-text">McKaynine Training</span>
    </div>
  </div>

  <div class="body">
    <h1>Hi {{ $handlerName }},</h1>

    @if($outcome === 'group_class')
      <span class="outcome-badge badge-group">✓ Group Classes Recommended</span>
    @elseif($outcome === 'private_lessons')
      <span class="outcome-badge badge-private">Private Lessons Recommended</span>
    @else
      <span class="outcome-badge badge-behaviourist">Behaviourist Referral</span>
    @endif

    @if($bodyText)
      {!! nl2br(e($bodyText)) !!}
    @else
      @if($outcome === 'group_class')
        <p>Congratulations! We're pleased to let you know that <strong>{{ $dogName }}</strong> is a great fit for our group training classes.</p>
      @elseif($outcome === 'private_lessons')
        <p>Thank you for bringing <strong>{{ $dogName }}</strong> along for an assessment. Based on what we observed, we recommend starting with private lessons to give you both the best possible foundation.</p>
      @else
        <p>Thank you for bringing <strong>{{ $dogName }}</strong> along for an assessment. After careful consideration, we'd like to refer you to a behaviourist for some specialist support.</p>
      @endif
    @endif

    @if($outcome === 'group_class' && ($recommendedClassName || $recommendedClassUrl))
    <div class="class-card">
      <h3>Recommended Class@if($recommendedClassName): {{ $recommendedClassName }}@endif</h3>
      @if($recommendedClassUrl)
        <p style="margin-bottom:12px;">Take a look at the class details and find a schedule that works for you.</p>
        <a href="{{ $recommendedClassUrl }}" class="btn btn-amber">View Class Info →</a>
      @endif
    </div>
    @endif

    @if($outcome === 'group_class' || $outcome === 'private_lessons')
    <p style="margin-top:20px;">Ready to get started? Complete your enrolment using the link below — it only takes a few minutes.</p>
    <a href="{{ route('enrol.complete') }}" class="btn">Complete Enrolment →</a>
    @endif

  </div>

  <div class="footer">
    <p>McKaynine Training &mdash; {{ config('app.url') }}</p>
  </div>
</div>
</body>
</html>
