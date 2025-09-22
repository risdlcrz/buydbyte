@extends('emails.layout')

@section('title', 'Verify Your Email - BuyDbyte')

@section('content')
<h2>Welcome to BuyDbyte, {{ $user->first_name }}! 🎉</h2>

<p>Thank you for joining BuyDbyte, your premier destination for computer hardware and peripherals.</p>

<p>To complete your account setup and start shopping for the latest tech gear, please verify your email address by clicking the button below:</p>

<div class="btn-container">
    <a href="{{ $verificationUrl }}" class="btn">
        ✅ Verify My Email Address
    </a>
</div>

<p>If the button doesn't work, you can copy and paste this link into your browser:</p>
<div class="token-box">
    {{ $verificationUrl }}
</div>

<div class="security-note">
    <strong>🔒 Security Note:</strong> This verification link will expire in 24 hours. If you didn't create an account with BuyDbyte, please ignore this email.
</div>

<p>Once verified, you'll be able to:</p>
<ul>
    <li>Browse our extensive catalog of computer hardware</li>
    <li>Get exclusive deals on the latest peripherals</li>
    <li>Track your orders and manage your account</li>
    <li>Receive personalized product recommendations</li>
</ul>

<p>Welcome to the BuyDbyte family!</p>

<p>Best regards,<br>
<strong>The BuyDbyte Team</strong></p>
@endsection