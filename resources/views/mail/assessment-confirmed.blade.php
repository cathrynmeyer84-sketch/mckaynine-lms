<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Assessment Confirmed</title>
<style>
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f1ea; margin: 0; padding: 24px; }
  .wrapper { max-width: 560px; margin: 0 auto; }
  .card { background: #fff; border-radius: 16px; padding: 32px; margin-bottom: 16px; }
  .logo { font-size: 20px; font-weight: 700; color: #1a2e44; margin-bottom: 24px; }
  h1 { font-size: 22px; font-weight: 700; color: #1a2e44; margin: 0 0 8px; }
  p { color: #4b5563; font-size: 15px; line-height: 1.6; margin: 0 0 12px; }
  .summary { background: #f5f1ea; border-radius: 12px; padding: 20px; margin: 20px 0; }
  .row { display: flex; gap: 8px; padding: 6px 0; font-size: 14px; }
  .lbl { color: #9ca3af; min-width: 80px; font-weight: 500; }
  .val { color: #111827; font-weight: 600; }
  .instructions { background: #fff8ed; border-left: 3px solid #f59e0b; border-radius: 0 8px 8px 0; padding: 16px; margin-top: 20px; font-size: 14px; color: #374151; line-height: 1.7; }
  .footer { text-align: center; font-size: 12px; color: #9ca3af; padding: 16px; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="card">
    <div class="logo">McKaynine</div>
    <h1>Assessment Confirmed! 🐾</h1>
    <p>Hi {{ $assessmentRequest->handler?->first_name }}, your assessment for <strong>{{ $assessmentRequest->dog?->name }}</strong> is booked. We look forward to meeting you both!</p>

    <div class="summary">
      <div class="row">
        <span class="lbl">Date</span>
        <span class="val">{{ \Carbon\Carbon::parse($assessmentRequest->slot?->date)->format('l, d F Y') }}</span>
      </div>
      <div class="row">
        <span class="lbl">Time</span>
        <span class="val">
          {{ \Carbon\Carbon::parse($assessmentRequest->slot?->start_time)->format('g:i A') }}
          @if($assessmentRequest->slot?->end_time)
            – {{ \Carbon\Carbon::parse($assessmentRequest->slot->end_time)->format('g:i A') }}
          @endif
        </span>
      </div>
      @if($location)
      <div class="row">
        <span class="lbl">Location</span>
        <span class="val">{{ $location }}</span>
      </div>
      @endif
      @if($assessmentRequest->slot?->notes)
      <div class="row">
        <span class="lbl">Notes</span>
        <span class="val">{{ $assessmentRequest->slot->notes }}</span>
      </div>
      @endif
    </div>

    @if($instructions)
    <p style="font-weight:600; color:#1a2e44; margin-bottom:4px;">On the day, please remember:</p>
    <div class="instructions">{{ $instructions }}</div>
    @else
    <div class="instructions">
      <strong>A few things to bring:</strong>
      <ul style="margin: 8px 0 0; padding-left: 20px;">
        <li>Your dog on a properly fitted collar or harness and leash</li>
        <li>High-value treats your dog loves</li>
        <li>Your vaccination card (if not already submitted)</li>
      </ul>
    </div>
    @endif
  </div>
  <div class="footer">McKaynine Dog Training · Questions? Reply to this email.</div>
</div>
</body>
</html>
