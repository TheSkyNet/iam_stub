# Email Service

The Email Service provides a flexible, provider-based email system for the Phalcon stub project. It supports multiple email providers and offers a unified interface for sending emails with various options and configurations.

## Overview

This folder contains the complete email service implementation:

- **EmailService.php** - Main email service class with provider abstraction
- **EmailProviderInterface.php** - Interface defining the contract for email providers
- **Providers/** - Directory containing email provider implementations
  - **MailHogProvider.php** - Development email provider using MailHog
  - **ResendProvider.php** - Production email provider using Resend API

## Architecture

The email system uses a provider pattern that allows switching between different email services without changing application code. This makes it easy to use different providers for development, testing, and production environments.

```
EmailService
    ↓
EmailProviderInterface
    ↓
┌─────────────────┬─────────────────┐
│  MailHogProvider │  ResendProvider │
└─────────────────┴─────────────────┘
```

## Components

### EmailService

**File:** `EmailService.php`  
**Namespace:** `IamLab\Core\Email`

The main email service class that provides a unified interface for sending emails regardless of the underlying provider.

#### Key Features

- Provider abstraction and automatic initialization
- Configuration management
- Error handling and logging
- Support for single and bulk email sending
- Automatic fallback handling

#### Methods

##### `send(string $to, string $subject, string $body, array $options = []): bool`
Sends an email to a single recipient.

**Parameters:**
- `$to` - Recipient email address
- `$subject` - Email subject
- `$body` - Email body (HTML or plain text)
- `$options` - Additional options (from_email, from_name, is_html, cc, bcc, etc.)

**Returns:** `bool` - True if email was sent successfully

```php
use IamLab\Core\Email\EmailService;

$emailService = new EmailService();

$result = $emailService->send(
    'user@example.com',
    'Welcome to Our Platform',
    '<h1>Welcome!</h1><p>Thank you for joining us.</p>',
    [
        'is_html' => true,
        'from_name' => 'Platform Team',
        'reply_to' => 'support@example.com'
    ]
);

if ($result) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email: " . $emailService->getLastError();
}
```

##### `sendBulk(array $recipients, string $subject, string $body, array $options = []): bool`
Sends an email to multiple recipients.

```php
$recipients = [
    'user1@example.com',
    'user2@example.com',
    'user3@example.com'
];

$result = $emailService->sendBulk(
    $recipients,
    'Newsletter Update',
    'Here is our latest newsletter content...',
    ['is_html' => false]
);
```

##### `getLastError(): string`
Returns the last error message if an operation failed.

##### `getProviderName(): string`
Returns the name of the currently configured provider.

##### `isReady(): bool`
Checks if the email service is properly configured and ready to send emails.

### EmailProviderInterface

**File:** `EmailProviderInterface.php`  
**Namespace:** `IamLab\Core\Email`

Interface that defines the contract all email providers must implement.

#### Required Methods

- `send()` - Send single email
- `sendBulk()` - Send bulk emails
- `validateConfig()` - Validate provider configuration
- `getLastError()` - Get last error message

### Email Providers

#### MailHogProvider

**File:** `Providers/MailHogProvider.php`  
**Purpose:** Development email testing using MailHog

MailHog is a development email testing tool that captures emails without actually sending them. Perfect for development and testing environments.

**Configuration:**
```php
// In config.php
'email' => [
    'provider' => 'mailhog',
    'mailhog' => [
        'host' => 'mailhog',
        'port' => 1025,
        'username' => '',
        'password' => '',
        'encryption' => '',
    ]
]
```

**Features:**
- SMTP-based email sending
- No authentication required
- Email capture for testing
- Web interface for viewing emails

#### ResendProvider

**File:** `Providers/ResendProvider.php`  
**Purpose:** Production email sending using Resend API

Resend is a modern email API service designed for developers, offering reliable email delivery with a simple API.

**Configuration:**
```php
// In config.php
'email' => [
    'provider' => 'resend',
    'resend' => [
        'api_key' => 'your_resend_api_key',
        'endpoint' => 'https://api.resend.com',
    ]
]
```

**Features:**
- RESTful API integration
- High deliverability rates
- Advanced email features (tags, tracking, etc.)
- Production-ready reliability

## Configuration

### Environment Variables

Configure email settings in your `.env` file:

```env
# Email Service Configuration
MAIL_PROVIDER=mailhog
MAIL_FROM_EMAIL=noreply@example.com
MAIL_FROM_NAME="Phalcon Stub"

# MailHog Configuration (for development)
MAILHOG_HOST=mailhog
MAILHOG_PORT=1025
MAILHOG_USERNAME=
MAILHOG_PASSWORD=
MAILHOG_ENCRYPTION=

# Resend Configuration (for production)
RESEND_API_KEY=your_api_key_here
RESEND_ENDPOINT=https://api.resend.com
```

### Configuration File

The email configuration is defined in `config/config.php`:

```php
'email' => [
    'provider' => App\Core\Helpers\env('MAIL_PROVIDER', 'mailhog'),
    'from_email' => App\Core\Helpers\env('MAIL_FROM_EMAIL', 'noreply@example.com'),
    'from_name' => App\Core\Helpers\env('MAIL_FROM_NAME', 'Phalcon Stub'),

    'mailhog' => [
        'host' => App\Core\Helpers\env('MAILHOG_HOST', 'mailhog'),
        'port' => App\Core\Helpers\env('MAILHOG_PORT', 1025),
        'username' => App\Core\Helpers\env('MAILHOG_USERNAME', ''),
        'password' => App\Core\Helpers\env('MAILHOG_PASSWORD', ''),
        'encryption' => App\Core\Helpers\env('MAILHOG_ENCRYPTION', ''),
    ],

    'resend' => [
        'api_key' => App\Core\Helpers\env('RESEND_API_KEY', ''),
        'endpoint' => App\Core\Helpers\env('RESEND_ENDPOINT', 'https://api.resend.com'),
    ],
]
```

## Usage Examples

### Basic Email Sending

```php
<?php

use IamLab\Core\Email\EmailService;

// Initialize email service
$emailService = new EmailService();

// Check if service is ready
if (!$emailService->isReady()) {
    die("Email service not configured: " . $emailService->getLastError());
}

// Send a simple email
$result = $emailService->send(
    'user@example.com',
    'Test Email',
    'This is a test email message.'
);

if ($result) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email: " . $emailService->getLastError();
}
```

### HTML Email with Options

```php
$htmlContent = '
<!DOCTYPE html>
<html>
<head>
    <title>Welcome Email</title>
</head>
<body>
    <h1>Welcome to Our Platform!</h1>
    <p>Thank you for joining us. Here are your next steps:</p>
    <ul>
        <li>Complete your profile</li>
        <li>Explore our features</li>
        <li>Connect with other users</li>
    </ul>
    <p>Best regards,<br>The Platform Team</p>
</body>
</html>
';

$result = $emailService->send(
    'newuser@example.com',
    'Welcome to Our Platform',
    $htmlContent,
    [
        'is_html' => true,
        'from_name' => 'Platform Team',
        'from_email' => 'welcome@platform.com',
        'reply_to' => 'support@platform.com',
        'cc' => ['manager@platform.com'],
        'bcc' => ['analytics@platform.com']
    ]
);
```

### Bulk Email Sending

```php
// Send newsletter to multiple users
$subscribers = [
    'user1@example.com',
    'user2@example.com',
    'user3@example.com'
];

$newsletterContent = '
<h2>Monthly Newsletter</h2>
<p>Here are the latest updates from our platform...</p>
<ul>
    <li>New feature: Advanced search</li>
    <li>Improved performance</li>
    <li>Bug fixes and improvements</li>
</ul>
';

$result = $emailService->sendBulk(
    $subscribers,
    'Monthly Newsletter - ' . date('F Y'),
    $newsletterContent,
    [
        'is_html' => true,
        'from_name' => 'Newsletter Team',
        'tags' => ['newsletter', 'monthly']
    ]
);

if ($result) {
    echo "Newsletter sent to " . count($subscribers) . " subscribers!";
} else {
    echo "Failed to send newsletter: " . $emailService->getLastError();
}
```

### Password Reset Email

```php
function sendPasswordResetEmail($userEmail, $resetToken) {
    $emailService = new EmailService();
    
    $resetUrl = "https://yoursite.com/reset-password?token=" . $resetToken;
    
    $emailBody = "
    <h2>Password Reset Request</h2>
    <p>You have requested to reset your password. Click the link below to reset it:</p>
    <p><a href=\"{$resetUrl}\" style=\"background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;\">Reset Password</a></p>
    <p>This link will expire in 1 hour.</p>
    <p>If you did not request this password reset, please ignore this email.</p>
    ";
    
    return $emailService->send(
        $userEmail,
        'Password Reset Request',
        $emailBody,
        [
            'is_html' => true,
            'from_name' => 'Security Team'
        ]
    );
}

// Usage
$success = sendPasswordResetEmail('user@example.com', 'secure_reset_token_here');
```

### Email Helper Function

The project includes a convenient helper function for quick email sending:

```php
use function App\Core\Helpers\email;

// Simple email using helper function
$result = email(
    'user@example.com',
    'Quick Message',
    'This is a quick message sent using the helper function.'
);

// HTML email with options using helper
$result = email(
    'user@example.com',
    'HTML Message',
    '<h1>Hello!</h1><p>This is an HTML message.</p>',
    [
        'is_html' => true,
        'from_name' => 'Helper Function'
    ]
);
```

## Development and Testing

### Using MailHog for Development

1. **Start MailHog** (included in Docker setup):
   ```bash
   ./phalcons up
   ```

2. **Configure for MailHog**:
   ```env
   MAIL_PROVIDER=mailhog
   ```

3. **Send test emails** and view them at `http://localhost:8025`

4. **Test email functionality**:
   ```php
   $emailService = new EmailService();
   $result = $emailService->send(
       'test@example.com',
       'Test Email',
       'This is a test email for development.'
   );
   ```

### Testing Email Templates

```php
// Test different email templates
function testEmailTemplates() {
    $emailService = new EmailService();
    
    $templates = [
        'welcome' => [
            'subject' => 'Welcome to Our Platform',
            'body' => '<h1>Welcome!</h1><p>Thank you for joining us.</p>'
        ],
        'notification' => [
            'subject' => 'New Notification',
            'body' => '<p>You have a new notification.</p>'
        ],
        'reminder' => [
            'subject' => 'Reminder',
            'body' => '<p>This is a friendly reminder.</p>'
        ]
    ];
    
    foreach ($templates as $type => $template) {
        $result = $emailService->send(
            'test@example.com',
            $template['subject'],
            $template['body'],
            ['is_html' => true]
        );
        
        echo "Template '{$type}': " . ($result ? 'Sent' : 'Failed') . "\n";
    }
}
```

## Error Handling

### Comprehensive Error Handling

```php
function sendEmailWithErrorHandling($to, $subject, $body, $options = []) {
    try {
        $emailService = new EmailService();
        
        // Check if service is ready
        if (!$emailService->isReady()) {
            throw new Exception("Email service not ready: " . $emailService->getLastError());
        }
        
        // Validate email address
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email address: {$to}");
        }
        
        // Send email
        $result = $emailService->send($to, $subject, $body, $options);
        
        if (!$result) {
            throw new Exception("Failed to send email: " . $emailService->getLastError());
        }
        
        return true;
        
    } catch (Exception $e) {
        // Log error
        error_log("Email sending failed: " . $e->getMessage());
        
        // Handle error based on context
        if (defined('DEBUG') && DEBUG) {
            throw $e; // Re-throw in debug mode
        }
        
        return false; // Silent fail in production
    }
}
```

### Provider-Specific Error Handling

```php
function handleEmailErrors($emailService) {
    $provider = $emailService->getProviderName();
    $error = $emailService->getLastError();
    
    switch ($provider) {
        case 'mailhog':
            if (strpos($error, 'connection') !== false) {
                echo "MailHog server is not running. Start it with: ./phalcons up";
            }
            break;
            
        case 'resend':
            if (strpos($error, 'API key') !== false) {
                echo "Invalid Resend API key. Check your RESEND_API_KEY configuration.";
            } elseif (strpos($error, 'rate limit') !== false) {
                echo "Rate limit exceeded. Please try again later.";
            }
            break;
    }
}
```

## Best Practices

### 1. Environment-Specific Configuration
Use different providers for different environments:

```php
// Development
MAIL_PROVIDER=mailhog

// Staging
MAIL_PROVIDER=resend

// Production
MAIL_PROVIDER=resend
```

### 2. Email Validation
Always validate email addresses before sending:

```php
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

if (!isValidEmail($recipientEmail)) {
    throw new InvalidArgumentException("Invalid email address");
}
```

### 3. Template Management
Create reusable email templates:

```php
class EmailTemplates {
    public static function welcome($userName) {
        return [
            'subject' => 'Welcome to Our Platform',
            'body' => "<h1>Welcome, {$userName}!</h1><p>Thank you for joining us.</p>"
        ];
    }
    
    public static function passwordReset($resetUrl) {
        return [
            'subject' => 'Password Reset Request',
            'body' => "<p>Click <a href=\"{$resetUrl}\">here</a> to reset your password.</p>"
        ];
    }
}
```

### 4. Async Email Sending
For high-volume applications, consider queuing emails:

```php
// Queue email for background processing
function queueEmail($to, $subject, $body, $options = []) {
    // Add to job queue (Redis, database, etc.)
    $job = [
        'type' => 'email',
        'data' => compact('to', 'subject', 'body', 'options'),
        'created_at' => time()
    ];
    
    // Queue implementation here
}
```

## Security Considerations

1. **Input Validation**: Always validate and sanitize email addresses and content
2. **Rate Limiting**: Implement rate limiting to prevent email abuse
3. **Authentication**: Verify user permissions before sending emails
4. **Content Filtering**: Filter email content to prevent spam and malicious content
5. **Logging**: Log email activities for audit purposes

## Troubleshooting

### Common Issues

1. **MailHog not receiving emails**:
   - Check if MailHog container is running: `docker ps`
   - Verify MailHog configuration in `.env`
   - Check MailHog web interface at `http://localhost:8025`

2. **Resend API errors**:
   - Verify API key is correct
   - Check API rate limits
   - Ensure from email is verified in Resend dashboard

3. **Configuration errors**:
   - Verify `.env` file is loaded
   - Check configuration values in `config/config.php`
   - Use `$emailService->isReady()` to check service status

### Debug Mode

Enable debug logging for email operations:

```php
// Add to your email sending code
if (defined('DEBUG') && DEBUG) {
    echo "Provider: " . $emailService->getProviderName() . "\n";
    echo "Ready: " . ($emailService->isReady() ? 'Yes' : 'No') . "\n";
    echo "Last Error: " . $emailService->getLastError() . "\n";
}
```

## Future Enhancements

The email service can be extended with:

- Additional providers (SendGrid, Amazon SES, etc.)
- Email templates engine
- Attachment support
- Email tracking and analytics
- Queue-based email processing
- Email scheduling
- Bounce and complaint handling
- Email validation service integration

## Dependencies

- Phalcon Framework
- cURL (for API-based providers)
- Helper functions from `App\Core\Helpers`

## Related Documentation

- [Helpers Documentation](../Helpers/README.md) - For the `email()` helper function
- [Main Project README](../../../README.md)
- [MailHog Documentation](https://github.com/mailhog/MailHog)
- [Resend Documentation](https://resend.com/docs)