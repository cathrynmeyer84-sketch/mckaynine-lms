<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing link request</title>
    <style>
        body { font-family: 'Trebuchet MS', Arial, sans-serif; background: #f4f4f0; margin: 0; padding: 0; }
        .wrapper { max-width: 560px; margin: 40px auto; padding: 0 20px; }
        .card { background: #ffffff; border-radius: 16px; padding: 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .logo { text-align: center; margin-bottom: 28px; }
        .logo span { font-family: 'Trebuchet MS', sans-serif; font-size: 1.3rem; font-weight: 700; color: #1A1D2E; letter-spacing: -0.5px; }
        h1 { font-size: 1.15rem; font-weight: 700; color: #1A1D2E; margin: 0 0 12px; }
        p { font-size: 0.9rem; color: #4b5563; line-height: 1.7; margin: 0 0 16px; }
        .info-box { background: #f8f7f4; border-radius: 10px; padding: 16px 20px; margin: 20px 0; }
        .info-box p { margin: 0; font-size: 0.88rem; color: #374151; }
        .info-box strong { color: #1A1D2E; }
        .actions { display: flex; gap: 12px; margin-top: 28px; }
        .btn { display: inline-block; padding: 12px 28px; border-radius: 10px; font-size: 0.9rem; font-weight: 600; text-decoration: none; text-align: center; }
        .btn-approve { background: #1A1D2E; color: #ffffff; }
        .btn-decline { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
        .note { font-size: 0.78rem; color: #9ca3af; margin-top: 20px; }
        .footer { text-align: center; margin-top: 28px; font-size: 0.78rem; color: #9ca3af; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        <div class="logo"><span>McKaynine</span></div>

        <h1>Billing link request</h1>

        <p>Hi {{ $accountHolder->name }},</p>

        <p>
            <strong>{{ $requesterName }}</strong> has submitted a McKaynine enrolment and listed you as the person
            responsible for their account. They are asking to have their class invoices added to your McKaynine billing account.
        </p>

        <div class="info-box">
            <p><strong>Who is requesting:</strong> {{ $requesterName }}</p>
            <p style="margin-top:8px;"><strong>Their dog:</strong> {{ $requesterDogName }}</p>
            <p style="margin-top:8px;"><strong>What this means:</strong> Their class fees will appear on your InvoicesOnline statement alongside your own.</p>
        </div>

        <p>Please review and let us know if you agree to this:</p>

        <div class="actions">
            <a href="{{ $approveUrl }}" class="btn btn-approve">Yes, approve link</a>
            <a href="{{ $declineUrl }}" class="btn btn-decline">No, decline</a>
        </div>

        <p class="note">
            This request expires in 7 days. If you take no action, {{ $requesterName }} will be set up with their own separate account.
            If you did not expect this request, you can safely ignore it or click Decline.
        </p>
    </div>
    <div class="footer">
        McKaynine Training &mdash; This email was sent because someone listed your email during enrolment.
    </div>
</div>
</body>
</html>
