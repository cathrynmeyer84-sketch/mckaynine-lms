<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New Assessment Request</title>
<style>
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f1ea; margin: 0; padding: 24px; }
  .wrapper { max-width: 560px; margin: 0 auto; }
  .card { background: #fff; border-radius: 16px; padding: 32px; margin-bottom: 16px; }
  .logo { font-size: 20px; font-weight: 700; color: #1a2e44; margin-bottom: 24px; }
  h1 { font-size: 22px; font-weight: 700; color: #1a2e44; margin: 0 0 8px; }
  p { color: #4b5563; font-size: 15px; line-height: 1.6; margin: 0 0 12px; }
  .detail-row { display: flex; gap: 8px; padding: 8px 0; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
  .detail-label { color: #9ca3af; min-width: 130px; font-weight: 500; }
  .detail-value { color: #111827; }
  .btn { display: inline-block; background: #1a2e44; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: 600; font-size: 14px; margin-top: 20px; }
  .footer { text-align: center; font-size: 12px; color: #9ca3af; padding: 16px; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="card">
    <div class="logo">McKaynine</div>
    <h1>New Assessment Request</h1>
    <p>A new assessment form has been submitted and is ready for review.</p>

    <div style="margin: 20px 0;">
      <div class="detail-row">
        <span class="detail-label">Handler</span>
        <span class="detail-value">{{ $assessmentRequest->handler?->first_name }} {{ $assessmentRequest->handler?->last_name }}</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Email</span>
        <span class="detail-value">{{ $assessmentRequest->handler?->user?->email }}</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Cell</span>
        <span class="detail-value">{{ $assessmentRequest->handler?->cell_number }}</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Dog</span>
        <span class="detail-value">{{ $assessmentRequest->dog?->name }}@if($assessmentRequest->dog?->breed) — {{ $assessmentRequest->dog->breed }}@endif</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Submitted</span>
        <span class="detail-value">{{ $assessmentRequest->created_at->format('d M Y \a\t g:i A') }}</span>
      </div>
    </div>

    <a href="{{ url(route('admin.assessments.show', $assessmentRequest)) }}" class="btn">Review Assessment →</a>
  </div>
  <div class="footer">McKaynine Dog Training · This is an automated notification.</div>
</div>
</body>
</html>
