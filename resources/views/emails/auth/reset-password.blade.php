@extends('emails.layout')

@section('title', 'Reset Your Password - BuyDbyte')

@section('content')
<h2>Password Reset Request 🔐</h2>

<p>Hello {{ $user->first_name }},</p>

<p>We received a request to reset the password for your BuyDbyte account. If you made this request, click the button below to reset your password:</p>

<div class="btn-container">
    <a href="{{ $resetUrl }}" class="btn">
        🔑 Reset My Password
    </a>
</div>

<p>If the button doesn't work, you can copy and paste this link into your browser:</p>
<div class="token-box">
    {{ $resetUrl }}
</div>

<div class="security-note">
    <strong>⚠️ Important:</strong> This password reset link will expire in 1 hour for your security. If you didn't request a password reset, please ignore this email - your account is still secure.
</div>

<p><strong>Why am I receiving this email?</strong></p>
<p>This email was sent because someone (hopefully you) requested a password reset for the BuyDbyte account associated with this email address.</p>

<p><strong>What should I do if I didn't request this?</strong></p>
<p>If you didn't request a password reset, you can safely ignore this email. Your password will remain unchanged and your account is still secure.</p>

<p>If you're having trouble accessing your account or have security concerns, please contact our support team at <a href="mailto:support@buydbyte.com">support@buydbyte.com</a>.</p>

<p>Best regards,<br>
<strong>The BuyDbyte Security Team</strong></p>
@endsection