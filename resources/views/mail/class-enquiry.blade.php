<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>New Enquiry</title>
<style>
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f1ea; margin: 0; padding: 24px; }
  .wrapper { max-width: 560px; margin: 0 auto; }
  .card { background: #fff; border-radius: 16px; padding: 32px; margin-bottom: 16px; }
  .logo { font-size: 20px; font-weight: 700; color: #1a2e44; margin-bottom: 24px; }
  h1 { font-size: 22px; font-weight: 700; color: #1a2e44; margin: 0 0 8px; }
  p { color: #4b5563; font-size: 15px; line-height: 1.6; margin: 0 0 12px; }
  .row { display: flex; gap: 8px; margin-bottom: 8px; }
  .label { font-size: 13px; font-weight: 600; color: #6b7280; min-width: 110px; }
  .value { font-size: 14px; color: #1f2937; }
  .highlight { background: #f5f1ea; border-radius: 12px; padding: 16px 20px; margin: 16px 0; }
  .footer { text-align: center; font-size: 12px; color: #9ca3af; padding: 16px; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="card">
    <div class="logo">{{ $branch->branch_name ?: 'McKaynine' }}</div>
    <h1>New Enquiry Received</h1>
    <p>Someone has enquired about <strong>{{ $classType->name }}</strong>{{ $specificClass ? ' — ' . $specificClass->name : '' }}.</p>

    <div class="highlight">
      <div class="row"><span class="label">Name</span><span class="value">{{ $data['name'] }}</span></div>
      <div class="row"><span class="label">Email</span><span class="value"><a href="mailto:{{ $data['email'] }}">{{ $data['email'] }}</a></span></div>
      @if(!empty($data['phone']))
      <div class="row"><span class="label">Phone</span><span class="value">{{ $data['phone'] }}</span></div>
      @endif
      <div class="row"><span class="label">Class</span><span class="value">{{ $classType->name }}{{ $specificClass ? ' — starting ' . $specificClass->start_date?->format('d M Y') : '' }}</span></div>
      @if(!empty($data['heard_from']))
      <div class="row"><span class="label">Heard from</span><span class="value">{{ $data['heard_from'] }}</span></div>
      @endif
    </div>

    <p style="font-weight:600;color:#1a2e44;margin-bottom:4px;">Message:</p>
    <p style="white-space:pre-line;">{{ $data['message'] }}</p>
  </div>
  <div class="footer">{{ $branch->branch_name ?: 'McKaynine' }} · Reply directly to this email to respond to {{ $data['name'] }}.</div>
</div>
</body>
</html>
