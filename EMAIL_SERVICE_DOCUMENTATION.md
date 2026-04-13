# Email Service Documentation

## Overview
This application now includes a reusable Email Service that can be used throughout the application for sending emails. The service includes error handling, logging, and support for both synchronous and asynchronous (queued) email sending.

## Features Implemented

### 1. EmailService Class
**Location:** `app/Services/EmailService.php`

A reusable service class for sending emails with built-in error handling and logging.

#### Usage Examples:

```php
use App\Services\EmailService;
use App\Mail\YourMailableClass;

// Send a single email
EmailService::send(
    to: 'recipient@example.com',
    name: 'Recipient Name',
    mailable: new YourMailableClass($data)
);

// Send with custom from address
EmailService::send(
    to: 'recipient@example.com',
    name: 'Recipient Name',
    mailable: new YourMailableClass($data),
    from: 'custom@domain.com',
    fromName: 'Custom Name'
);

// Queue email for async sending
EmailService::queue(
    to: 'recipient@example.com',
    name: 'Recipient Name',
    mailable: new YourMailableClass($data)
);

// Send bulk emails
$recipients = [
    'user1@example.com' => 'User One',
    'user2@example.com' => 'User Two',
];
EmailService::sendBulk($recipients, new YourMailableClass($data));
```

### 2. Forgot Password Feature

#### Routes:
- `GET /forgot-password` - Show forgot password form
- `POST /forgot-password` - Send reset link email
- `GET /reset-password/{token}` - Show reset password form
- `POST /reset-password` - Process password reset

#### Files Created:

1. **Controller:** `app/Http/Controllers/Auth/ForgotPasswordController.php`
   - Handles forgot password form display
   - Sends reset link via email
   - Processes password reset
   - Token validation and expiry checking

2. **Mailable:** `app/Mail/ForgotPasswordRequest.php`
   - Email template for password reset
   - Includes reset URL and expiry time

3. **Email Template:** `resources/views/emails/forgot-password.blade.php`
   - Beautiful HTML email template
   - Responsive design
   - Security warnings and instructions

4. **Views:**
   - `resources/views/auth/forgot-password.blade.php` - Forgot password form
   - `resources/views/auth/reset-password.blade.php` - Reset password form

#### Security Features:
- Token expiry: 60 minutes
- Tokens are hashed before storage
- Tokens are deleted after use
- Failed attempts are logged

## Configuration

### Email Settings (.env file)

For the forgot password feature to work, you need to configure your email settings in the `.env` file:

#### For Development (using log driver):
```env
MAIL_MAILER=log
```
Emails will be written to `storage/logs/laravel.log`

#### For Production (using SMTP - Gmail example):
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_SCHEME=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Note for Gmail:** You need to use an App Password, not your regular Gmail password.
1. Go to your Google Account settings
2. Security → 2-Step Verification → App passwords
3. Generate a new app password for "Mail"
4. Use this password in your `.env` file

#### Alternative: Mailtrap (for testing)
```env
MAIL_MAILER=smtp
MAIL_HOST=mailpit.example.com
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
```

## How to Use Forgot Password

1. Go to the login page
2. Click "Lupa password?" link
3. Enter your registered email address
4. Click "Kirim Link Reset Password"
5. Check your email inbox
6. Click the reset link in the email
7. Enter your new password
8. Click "Reset Password"
9. You will be redirected to login with success message

## Creating New Email Templates

To create a new email template for other features:

### 1. Create a Mailable Class
```bash
php artisan make:mail WelcomeEmail
```

### 2. Define the Email Content
```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Selamat Datang - ' . config('app.name'),
            from: new Address(
                config('mail.from.address'),
                config('mail.from.name')
            ),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
            with: [
                'userName' => $this->user->name,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
```

### 3. Create the Email Template
Create `resources/views/emails/welcome.blade.php`

### 4. Send the Email
```php
use App\Services\EmailService;
use App\Mail\WelcomeEmail;

EmailService::send(
    $user->email,
    $user->name,
    new WelcomeEmail($user)
);
```

## Database Table

The `password_reset_tokens` table is used to store password reset tokens:

```sql
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
);
```

If the table doesn't exist, run:
```bash
php artisan migrate
```

## Testing

### Testing Forgot Password:

1. Make sure you have a user with an email in the database
2. Visit `/forgot-password`
3. Enter the email address
4. Check the logs or your email inbox
5. Click the reset link
6. Set a new password

### Testing with Log Driver:

If using `MAIL_MAILER=log`, check `storage/logs/laravel.log` for the email content.

## Troubleshooting

### Email not being sent:
1. Check `.env` MAIL_MAILER setting
2. Verify email credentials
3. Check `storage/logs/laravel.log` for errors
4. For Gmail, ensure App Password is used (not regular password)
5. Check spam/junk folder

### Token expired error:
- Tokens expire after 60 minutes
- Request a new reset link if expired

### Table already exists error:
- The `password_reset_tokens` table already exists in your database
- You can skip the migration or delete the migration file

## Best Practices

1. **Always use EmailService** for sending emails - don't use `Mail::to()` directly
2. **Queue emails** for better performance using `EmailService::queue()`
3. **Use appropriate email drivers** for production (not 'log')
4. **Test email functionality** in staging before production
5. **Monitor email logs** regularly for issues
6. **Rate limit** password reset requests to prevent abuse

## Future Enhancements

Potential features to add:
- Email verification on registration
- Welcome email for new users
- Notification emails for various events
- Email templates for different purposes
- Email queue monitoring
- Email delivery tracking
- Two-factor authentication via email
