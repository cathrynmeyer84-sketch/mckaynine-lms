<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Book Your Assessment</title>
<style>
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f1ea; margin: 0; padding: 24px; }
  .wrapper { max-width: 560px; margin: 0 auto; }
  .card { background: #fff; border-radius: 16px; padding: 32px; margin-bottom: 16px; }
  .logo { font-size: 20px; font-weight: 700; color: #1a2e44; margin-bottom: 24px; }
  h1 { font-size: 22px; font-weight: 700; color: #1a2e44; margin: 0 0 8px; }
  p { color: #4b5563; font-size: 15px; line-height: 1.6; margin: 0 0 12px; }
  .highlight { background: #f5f1ea; border-radius: 12px; padding: 16px 20px; margin: 20px 0; }
  .btn { display: inline-block; background: #1a2e44; color: #fff !important; text-decoration: none; padding: 14px 28px; border-radius: 10px; font-weight: 600; font-size: 15px; margin-top: 8px; }
  .footer { text-align: center; font-size: 12px; color: #9ca3af; padding: 16px; }
  .url { font-size: 12px; color: #9ca3af; word-break: break-all; margin-top: 12px; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="card">
    <div class="logo">McKaynine</div>
    <h1>Hi {{ $assessmentRequest->handler?->first_name }}!</h1>
    <p>
      Thank you for submitting your assessment questionnaire for <strong>{{ $assessmentRequest->dog?->name }}</strong>.
      We've reviewed your form and everything looks great — we're ready to schedule your assessment.
    </p>
    <p>
      Please use the button below to choose a date and time that works for you. The link shows all available slots for the next 30 days.
    </p>

    <div class="highlight">
      <p style="margin:0; font-size:14px; color:#6b7280;">This link is personalised for you — please don't share it.</p>
    </div>

    <a href="{{ $bookingUrl }}" class="btn">Choose Your Assessment Time →</a>

    <p class="url">Or copy this link into your browser:<br>{{ $bookingUrl }}</p>
  </div>
  <div class="footer">McKaynine Dog Training · Questions? Reply to this email.</div>
</div>
</body>
</html>
