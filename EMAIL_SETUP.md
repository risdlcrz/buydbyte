# BuyDbyte Email Configuration Guide

## 📧 Email Templates Location

Your email templates are now set up in the following structure:

```
resources/views/emails/
├── layout.blade.php          # Main email template layout
└── auth/
    ├── verify-email.blade.php  # Email verification template
    └── reset-password.blade.php # Password reset template
```

## 🎯 Mailable Classes Location

The mailable classes that handle sending emails are located at:

```
app/Mail/Auth/
├── VerifyEmail.php    # Handles email verification
└── ResetPassword.php  # Handles password reset
```

## ⚙️ Mail Configuration (.env)

To send actual emails, configure your `.env` file with your email settings:

### For Gmail (SMTP):
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@buydbyte.com
MAIL_FROM_NAME="BuyDbyte"
```

### For Mailtrap (Testing):
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@buydbyte.com
MAIL_FROM_NAME="BuyDbyte"
```

### For SendGrid:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@buydbyte.com
MAIL_FROM_NAME="BuyDbyte"
```

## 🧪 Testing Email Templates

You can test your email templates using these Artisan commands:

### Preview Email in Browser:
Create a route to preview emails (for development only):

```php
// In routes/web.php (remove in production)
Route::get('/preview-email', function () {
    $user = \App\Models\User::first();
    $token = \Illuminate\Support\Str::random(60);
    
    return new \App\Mail\Auth\VerifyEmail($user, $token);
});
```

### Send Test Email:
```bash
php artisan tinker

# In tinker:
$user = App\Models\User::first();
$token = Str::random(60);
Mail::to('test@example.com')->send(new App\Mail\Auth\VerifyEmail($user, $token));
```

## 🎨 Customizing Email Templates

### Modify the Design:
Edit `resources/views/emails/layout.blade.php` to change:
- Colors and branding
- Logo and header design  
- Footer information
- Overall styling

### Modify Email Content:
Edit the individual email templates:
- `verify-email.blade.php` - Registration confirmation
- `reset-password.blade.php` - Password reset instructions

### Add New Email Types:
1. Create new mailable: `php artisan make:mail NewEmailType`
2. Create new template: `resources/views/emails/new-email.blade.php`
3. Configure the mailable to use your template

## 🚀 Production Recommendations

1. **Use a dedicated email service** (SendGrid, Mailgun, AWS SES)
2. **Set up email queues** for better performance
3. **Add email tracking** and analytics
4. **Test thoroughly** with different email clients
5. **Set up proper SPF/DKIM records** for deliverability

## 🔄 Queue Email Sending (Recommended)

For better performance, queue your emails:

1. Make your mailables implement `ShouldQueue`:
```php
class VerifyEmail extends Mailable implements ShouldQueue
```

2. Configure your queue driver in `.env`:
```env
QUEUE_CONNECTION=database
```

3. Run queue worker:
```bash
php artisan queue:work
```

Your email templates are now fully integrated and ready to use! 🎉