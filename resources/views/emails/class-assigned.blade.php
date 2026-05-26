<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: Arial, sans-serif; font-size: 15px; color: #333; line-height: 1.6; background: #f9f9f9; margin: 0; padding: 0; }
  .wrapper { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; padding: 32px 40px; border: 1px solid #e5e7eb; }
  p { margin: 0 0 14px; }
  a { color: #001d6d; }
  .dates-box { background: #f3f4f6; border-radius: 8px; padding: 16px 20px; margin: 20px 0; }
  .dates-box h3 { margin: 0 0 10px; font-size: 14px; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; }
  .dates-box ul { margin: 0; padding: 0; list-style: none; }
  .dates-box li { padding: 5px 0; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
  .dates-box li:last-child { border-bottom: none; }
  .btn-row { margin: 24px 0 8px; display: flex; gap: 12px; flex-wrap: wrap; }
  .btn { display: inline-block; padding: 12px 24px; border-radius: 8px; font-size: 15px; font-weight: 600; text-decoration: none; }
  .btn-primary { background: #001d6d; color: #ffffff !important; }
  .btn-outline { background: #ffffff; color: #001d6d !important; border: 2px solid #001d6d; }
  .note { font-size: 13px; color: #6b7280; margin-top: 8px; }
</style>
</head>
<body>
<div class="wrapper">

    {!! nl2br(e($emailBody)) !!}

    @if(count($classDates))
    <div class="dates-box">
        <h3>Your class schedule</h3>
        <ul>
            @foreach($classDates as $date)
            <li>{{ $date }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="btn-row">
        <a href="{{ $classUrl }}" class="btn btn-primary">View My Class</a>
        <a href="{{ $resourcesUrl }}" class="btn btn-outline">General Resources</a>
    </div>

    <p class="note">Class-specific resources and weekly content will become available in the app as your class progresses — keep an eye on your inbox!</p>

</div>
</body>
</html>
