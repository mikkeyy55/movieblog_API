<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $type === 'admin' ? 'Admin' : '' }} Verification Code - Movie Blog</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        .header h1 {
            color: #ff6b35;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            color: #6c757d;
            margin: 10px 0 0 0;
            font-size: 16px;
        }
        .otp-container {
            text-align: center;
            margin: 30px 0;
            padding: 25px;
            background-color: #fff3e0;
            border-radius: 8px;
            border-left: 4px solid #ff6b35;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            color: #ff6b35;
            letter-spacing: 8px;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
        }
        .otp-label {
            font-size: 18px;
            color: #495057;
            margin-bottom: 10px;
        }
        .content {
            margin: 25px 0;
            line-height: 1.8;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">
                @if($type === 'admin')
                    🛡️
                @else
                    🔐
                @endif
            </div>
            <h1>{{ $type === 'admin' ? 'Admin' : '' }} Verification Code</h1>
            <p>Movie Blog {{ $type === 'admin' ? 'Administration' : 'Authentication' }}</p>
        </div>

        <div class="content">
            <p>Hello,</p>

            <p>You have requested to {{ $type === 'admin' ? 'access the admin panel' : 'log in to your account' }} for Movie Blog. Please use the verification code below to complete your {{ $type === 'admin' ? 'admin login' : 'login' }}:</p>
        </div>

        <div class="otp-container">
            <div class="otp-label">Your Verification Code</div>
            <div class="otp-code">{{ $otp }}</div>
            <p style="margin: 10px 0 0 0; font-size: 14px; color: #6c757d;">
                This code will expire in 10 minutes
            </p>
        </div>

        <div class="content">
            <p>If you didn't request this verification code, please ignore this email. Your account remains secure.</p>

            @if($type === 'admin')
            <p><strong>Note:</strong> This is an admin verification code and should only be used by authorized administrators.</p>
            @endif
        </div>

        <div class="warning">
            <strong>⚠️ Security Notice:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Never share this code with anyone</li>
                <li>We will never ask for this code via phone or email</li>
                <li>This code expires in 10 minutes</li>
                <li>You have maximum 3 attempts to enter the correct code</li>
            </ul>
        </div>

        <div class="footer">
            <p>
                <strong>Movie Blog</strong><br>
                This is an automated message, please do not reply to this email.
            </p>
            <p style="margin-top: 15px; font-size: 12px;">
                © {{ date('Y') }} Movie Blog. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>

