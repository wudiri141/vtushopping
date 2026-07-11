<?php

function app_absolute_url(string $path = ''): string
{
    $configured = getenv('APP_URL') ?: 'https://vtushopping.vtutopup.com.ng';
    return rtrim($configured, '/') . '/' . ltrim($path, '/');
}

function send_shop_email(string $email, string $name, string $subject, string $body, string $plain = ''): bool
{
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: VTU Shopping Store <' . (getenv('MAIL_FROM') ?: 'support@vtutopup.com.ng') . '>',
    ];

    $sent = @mail($email, $subject, $body, implode("\r\n", $headers));

    if (!$sent) {
        error_log('Mail failed: ' . $subject . ' to ' . $email);
    }

    return $sent;
}

function email_shell(string $title, string $content): string
{
    return "
        <div style='font-family:Arial,sans-serif;background:#fff8ef;padding:24px;'>
            <div style='max-width:620px;margin:auto;background:#ffffff;border:1px solid #f0dfc8;border-radius:16px;padding:30px;'>
                <h1 style='margin:0 0 16px;color:#333747;font-size:24px;'>{$title}</h1>
                {$content}
                <hr style='border:none;border-top:1px solid #f0dfc8;margin:26px 0;'>
                <p style='margin:0;color:#9a6b23;font-size:12px;text-align:center;'>VTU Shopping Store</p>
            </div>
        </div>
    ";
}

function send_verification_email(string $email, string $name, string $token): bool
{
    $link = app_absolute_url('verify-email?token=' . urlencode($token));
    $content = "
        <p style='color:#555b6c;line-height:1.7;'>Hello " . htmlspecialchars($name) . ", welcome to VTU Shopping Store. Please verify your email address to activate your account.</p>
        <p style='text-align:center;margin:24px 0;'><a href='{$link}' style='background:#ff9700;color:#fff;text-decoration:none;padding:13px 22px;border-radius:999px;font-weight:bold;'>Verify Email</a></p>
        <p style='color:#777c8b;font-size:14px;word-break:break-word;'>If the button does not work, open this link: {$link}</p>
    ";

    return send_shop_email($email, $name, 'Verify your VTU Shopping Store email', email_shell('Verify your email', $content), "Verify your email: {$link}");
}

function send_password_reset_email(string $email, string $name, string $token): bool
{
    $link = app_absolute_url('reset-password?token=' . urlencode($token));
    $content = "
        <p style='color:#555b6c;line-height:1.7;'>Hello " . htmlspecialchars($name) . ", use the button below to create a new password. This link expires in 1 hour.</p>
        <p style='text-align:center;margin:24px 0;'><a href='{$link}' style='background:#ff9700;color:#fff;text-decoration:none;padding:13px 22px;border-radius:999px;font-weight:bold;'>Reset Password</a></p>
        <p style='color:#777c8b;font-size:14px;word-break:break-word;'>If the button does not work, open this link: {$link}</p>
    ";

    return send_shop_email($email, $name, 'Reset your VTU Shopping Store password', email_shell('Reset your password', $content), "Reset your password: {$link}");
}

function send_login_otp_email(string $email, string $name, string $otp): bool
{
    $content = "
        <p style='color:#555b6c;line-height:1.7;'>Hello " . htmlspecialchars($name) . ", use this OTP to complete your login.</p>
        <div style='text-align:center;margin:26px 0;'><span style='display:inline-block;background:#fff3e2;color:#e88600;padding:15px 24px;border-radius:12px;font-size:30px;font-weight:bold;letter-spacing:8px;'>{$otp}</span></div>
        <p style='color:#777c8b;font-size:14px;'>This OTP expires in 10 minutes.</p>
    ";

    return send_shop_email($email, $name, 'Your VTU Shopping Store login OTP', email_shell('Login verification', $content), "Your OTP is {$otp}");
}
