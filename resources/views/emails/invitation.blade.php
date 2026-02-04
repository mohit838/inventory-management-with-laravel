<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 40px auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; }
        .button { display: inline-block; padding: 12px 24px; background-color: #4f46e5; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px; }
        .footer { margin-top: 30px; font-size: 0.8em; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to {{ config('app.name') }}!</h1>
        <p>You have been invited to join our platform as an <strong>{{ ucfirst($invitation->role) }}</strong>.</p>
        <p>Please click the button below to complete your registration. This link will expire in 24 hours.</p>
        
        <a href="{{ $registerUrl }}" class="button">Accept Invitation</a>
        
        <p>Or copy and paste this link into your browser:</p>
        <p>{{ $registerUrl }}</p>
        
        <div class="footer">
            If you did not expect this invitation, you can safely ignore this email.
        </div>
    </div>
</body>
</html>
