<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.email_subject_budget_default') }}</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <p>{{ config('app.name') }}</p>
    <p>{{ $alertMessage }}</p>
    <p style="margin-top: 24px; font-size: 12px; color: #666;">{{ __('messages.email_auto_footer', ['app' => config('app.name')]) }}</p>
</body>
</html>
