# Core Framework Components

The Core directory contains the foundational components of the Phalcon stub project. These components provide essential functionality for building modern web applications, including API frameworks, data manipulation, email services, environment management, helper utilities, and real-time communication.

## Overview

The Core framework is designed with modularity, reusability, and maintainability in mind. Each component is self-contained with its own documentation, examples, and clear interfaces, making it easy to understand, extend, and maintain.

## Architecture

```
IamLab/Core/
├── API/           # RESTful API framework and base classes
├── Collection/    # Enhanced data collection utilities
├── Email/         # Multi-provider email service system
├── Env/           # Environment variable management
├── Helpers/       # Utility functions and common operations
└── Pusher/        # Real-time WebSocket communication
```

## Components

### 🔌 API Framework
**Location:** `API/`  
**Documentation:** [API README](API/README.md)

Provides a robust foundation for building RESTful APIs with standardized response handling, error management, and data processing.

**Key Features:**
- Base API classes with common functionality
- Standardized JSON response handling
- Entity models with casting and amendments
- Parameter extraction and validation
- Error handling and logging

**Main Classes:**
- `aAPI` - Abstract base API class
- `Entity` - Enhanced model base class
- `Rest` - REST API utilities

### 📦 Collection Utilities
**Location:** `Collection/`  
**Documentation:** [Collection README](Collection/README.md)

Enhanced collection class that extends Phalcon's base collection with additional methods for data manipulation and processing.

**Key Features:**
- Functional programming methods (map, filter, each)
- Chainable operations
- Enhanced data processing
- Laravel-style collection methods

**Main Classes:**
- `Collection` - Enhanced collection with additional methods

### 📧 Email Service System
**Location:** `Email/`  
**Documentation:** [Email README](Email/README.md)

Comprehensive email service with multiple provider support, making it easy to send emails in development and production environments.

**Key Features:**
- Multiple email providers (MailHog, Resend)
- Environment-based configuration
- HTML and plain text support
- Bulk email capabilities
- Error handling and logging

**Main Classes:**
- `EmailService` - Main email service
- `EmailProviderInterface` - Provider interface
- `MailHogProvider` - Development email provider
- `ResendProvider` - Production email provider

### 🌍 Environment Management
**Location:** `Env/`  
**Documentation:** [Env README](Env/README.md)

Simple and reliable environment variable loading from .env files with validation and error handling.

**Key Features:**
- .env file parsing
- Environment variable validation
- Error handling for missing files
- Support for different environments

**Main Classes:**
- `Env` - Environment variable loader

### 🛠️ Helper Utilities
**Location:** `Helpers/`  
**Documentation:** [Helpers README](Helpers/README.md)

Collection of utility functions that simplify common tasks throughout the application, including configuration access, data manipulation, encryption, and debugging.

**Key Features:**
- Configuration and environment helpers
- Data casting and object manipulation
- Encryption/decryption utilities
- Email helper functions
- Debugging tools
- Collection utilities

**Main Files:**
- `util.php` - Core utilities (config, DI, email, debugging)
- `array.php` - Array and object manipulation
- `crypt.php` - Encryption and decryption
- `include.php` - Autoloader for all helpers

### ⚡ Real-time Communication
**Location:** `Pusher/`  
**Documentation:** [Pusher README](Pusher/README.md)

WebSocket-based real-time communication using Pusher.js for live updates, notifications, and interactive features.

**Key Features:**
- Event triggering and broadcasting
- Channel management (public, private, presence)
- Authentication for private channels
- Webhook verification
- Error handling and logging

**Main Classes:**
- `PusherService` - Main Pusher service

## Quick Start Guide

### 1. Basic API Development

```php
<?php

use IamLab\Core\API\aAPI;

class UserAPI extends aAPI
{
    public function getUserAction()
    {
        $userId = $this->getParam('id', null, 'int');
        $user = User::findFirst($userId);
        
        if (!$user) {
            $this->dispatchError(['message' => 'User not found']);
            return;
        }
        
        $this->dispatch(['success' => true, 'data' => $user]);
    }
}
```

### 2. Email Notifications

```php
<?php

use function App\Core\Helpers\email;

// Send welcome email
$result = email(
    'user@example.com',
    'Welcome to Our Platform',
    '<h1>Welcome!</h1><p>Thank you for joining us.</p>',
    ['is_html' => true]
);
```

### 3. Data Processing

```php
<?php

use function App\Core\Helpers\{collect, cast};

// Process user data
$users = collect($rawUserData)
    ->map(function($user) {
        $user['id'] = cast($user['id'], 'int');
        $user['is_active'] = cast($user['is_active'], 'bool');
        return $user;
    })
    ->filter(fn($user) => $user['is_active'])
    ->each(fn($user) => echo "Processing: {$user['name']}\n");
```

### 4. Real-time Updates

```php
<?php

use IamLab\Core\Pusher\PusherService;

$pusher = new PusherService();

// Send real-time notification
$pusher->trigger(
    'notifications',
    'new-message',
    ['message' => 'Hello World!', 'timestamp' => time()]
);
```

### 5. Configuration Management

```php
<?php

use function App\Core\Helpers\{config, env, loadEnv};

// Load environment
loadEnv('.env');

// Get configuration
$dbHost = config('database.host');
$appName = env('APP_NAME', 'Default App');
```

## Common Patterns

### 1. Service Layer Pattern

```php
<?php

use IamLab\Core\Email\EmailService;
use IamLab\Core\Pusher\PusherService;
use function App\Core\Helpers\config;

class NotificationService
{
    private EmailService $emailService;
    private PusherService $pusherService;

    public function __construct()
    {
        $this->emailService = new EmailService();
        $this->pusherService = new PusherService();
    }

    public function sendWelcomeNotification($user)
    {
        // Send email
        $emailSent = $this->emailService->send(
            $user->getEmail(),
            'Welcome!',
            $this->getWelcomeEmailTemplate($user),
            ['is_html' => true]
        );

        // Send real-time notification
        $realtimeSent = $this->pusherService->trigger(
            "user-{$user->getId()}",
            'welcome-notification',
            ['message' => 'Welcome to our platform!']
        );

        return $emailSent && $realtimeSent;
    }
}
```

### 2. Data Processing Pipeline

```php
<?php

use function App\Core\Helpers\{collect, cast, merge_objects};

class DataProcessor
{
    public function processUserData($rawData)
    {
        return collect($rawData)
            ->map([$this, 'normalizeUser'])
            ->filter([$this, 'validateUser'])
            ->map([$this, 'enrichUser'])
            ->each([$this, 'saveUser']);
    }

    private function normalizeUser($user)
    {
        return [
            'id' => cast($user['id'], 'int'),
            'email' => strtolower(trim($user['email'])),
            'is_active' => cast($user['is_active'], 'bool'),
            'created_at' => $user['created_at']
        ];
    }

    private function validateUser($user)
    {
        return filter_var($user['email'], FILTER_VALIDATE_EMAIL) !== false;
    }

    private function enrichUser($user)
    {
        $enrichment = (object)[
            'display_name' => $user['first_name'] . ' ' . $user['last_name'],
            'avatar_url' => $this->generateAvatarUrl($user['email'])
        ];

        return merge_objects((object)$user, $enrichment);
    }
}
```

### 3. API Response Standardization

```php
<?php

use IamLab\Core\API\aAPI;
use function App\Core\Helpers\cast;

class ProductAPI extends aAPI
{
    public function getProductsAction()
    {
        try {
            $page = cast($this->getParam('page', 1), 'int');
            $limit = cast($this->getParam('limit', 10), 'int');
            
            $products = Product::find([
                'limit' => $limit,
                'offset' => ($page - 1) * $limit
            ]);

            $this->dispatch([
                'success' => true,
                'data' => $products->toArray(),
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => Product::count()
                ]
            ]);
        } catch (Exception $e) {
            $this->dispatchError([
                'success' => false,
                'message' => 'Failed to fetch products',
                'error' => $e->getMessage()
            ]);
        }
    }
}
```

## Configuration

### Environment Setup

Create a `.env` file with the necessary configuration:

```env
# Application
APP_NAME=Phalcon Stub
APP_ENV=local
APP_DEBUG=true

# Database
DB_HOST=localhost
DB_USERNAME=root
DB_PASSWORD=secret

# Email
MAIL_PROVIDER=mailhog
MAIL_FROM_EMAIL=noreply@example.com

# Pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
```

### Service Registration

Register Core services in your DI container:

```php
<?php

use Phalcon\Di\FactoryDefault;
use IamLab\Core\Email\EmailService;
use IamLab\Core\Pusher\PusherService;

$di = new FactoryDefault();

// Email service
$di->setShared('emailService', function() {
    return new EmailService();
});

// Pusher service
$di->setShared('pusherService', function() {
    return new PusherService();
});

// Load helper functions
require_once 'IamLab/Core/Helpers/include.php';
```

## Testing

### Unit Testing

Each Core component includes examples and can be tested independently:

```php
<?php

use PHPUnit\Framework\TestCase;
use IamLab\Core\Collection\Collection;

class CollectionTest extends TestCase
{
    public function testMapMethod()
    {
        $collection = new Collection([1, 2, 3]);
        $result = $collection->map(fn($item) => $item * 2);
        
        $this->assertEquals([2, 4, 6], $result->toArray());
    }
}
```

### Integration Testing

Test Core components working together:

```php
<?php

// Test email and notification integration
$emailService = new EmailService();
$pusherService = new PusherService();

// Send email
$emailResult = $emailService->send(
    'test@example.com',
    'Test Subject',
    'Test Body'
);

// Send real-time notification
$pusherResult = $pusherService->trigger(
    'test-channel',
    'test-event',
    ['message' => 'Test message']
);

assert($emailResult === true);
assert($pusherResult === true);
```

## Best Practices

### 1. Dependency Injection

Use dependency injection for Core services:

```php
<?php

class UserService
{
    public function __construct(
        private EmailService $emailService,
        private PusherService $pusherService
    ) {}

    public function createUser($userData)
    {
        $user = new User($userData);
        $user->save();

        // Send welcome email
        $this->emailService->send(
            $user->getEmail(),
            'Welcome!',
            $this->getWelcomeTemplate($user)
        );

        // Send real-time notification
        $this->pusherService->trigger(
            'admin-notifications',
            'new-user',
            ['user_id' => $user->getId()]
        );

        return $user;
    }
}
```

### 2. Error Handling

Implement comprehensive error handling:

```php
<?php

use function App\Core\Helpers\{email, config};

class NotificationService
{
    public function sendNotification($userId, $message)
    {
        try {
            $user = User::findFirst($userId);
            if (!$user) {
                throw new InvalidArgumentException("User not found: {$userId}");
            }

            $result = email(
                $user->getEmail(),
                'Notification',
                $message
            );

            if (!$result) {
                throw new RuntimeException("Failed to send email notification");
            }

            return true;
        } catch (Exception $e) {
            error_log("Notification failed: " . $e->getMessage());
            return false;
        }
    }
}
```

### 3. Configuration Management

Use environment-based configuration:

```php
<?php

use function App\Core\Helpers\{env, config};

// Good: Environment-based
$apiKey = env('THIRD_PARTY_API_KEY');
$dbHost = config('database.host');

// Avoid: Hard-coded values
$apiKey = 'hardcoded-key';
$dbHost = 'localhost';
```

### 4. Data Validation

Validate and cast data appropriately:

```php
<?php

use function App\Core\Helpers\cast;

class UserAPI extends aAPI
{
    public function updateUserAction()
    {
        $userId = cast($this->getParam('id'), 'int');
        $isActive = cast($this->getParam('is_active'), 'bool');
        $email = filter_var($this->getParam('email'), FILTER_VALIDATE_EMAIL);

        if (!$email) {
            $this->dispatchError(['message' => 'Invalid email address']);
            return;
        }

        // Process update...
    }
}
```

## Performance Considerations

### 1. Lazy Loading

Load services only when needed:

```php
<?php

class ServiceManager
{
    private ?EmailService $emailService = null;
    private ?PusherService $pusherService = null;

    public function getEmailService(): EmailService
    {
        if ($this->emailService === null) {
            $this->emailService = new EmailService();
        }
        return $this->emailService;
    }
}
```

### 2. Caching

Cache frequently accessed data:

```php
<?php

use function App\Core\Helpers\config;

class ConfigCache
{
    private static array $cache = [];

    public static function get($key, $default = null)
    {
        if (!isset(self::$cache[$key])) {
            self::$cache[$key] = config($key, $default);
        }
        return self::$cache[$key];
    }
}
```

### 3. Batch Operations

Use batch operations when possible:

```php
<?php

// Good: Batch email sending
$emailService->sendBulk(
    ['user1@example.com', 'user2@example.com'],
    'Newsletter',
    $content
);

// Good: Batch Pusher events
$pusherService->triggerBatch(
    ['channel1', 'channel2'],
    'update',
    $data
);
```

## Security Considerations

### 1. Input Validation

Always validate and sanitize input:

```php
<?php

use function App\Core\Helpers\cast;

class SecureAPI extends aAPI
{
    protected function getSecureParam($name, $type = 'string', $default = null)
    {
        $value = $this->getParam($name, $default);
        
        // Validate and cast
        $value = cast($value, $type);
        
        // Additional sanitization
        if ($type === 'string') {
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
        
        return $value;
    }
}
```

### 2. Encryption

Use encryption for sensitive data:

```php
<?php

use function App\Core\Helpers\{crypt, decrypt};

class SecureDataService
{
    public function storeSecureData($data)
    {
        $encrypted = crypt(json_encode($data));
        // Store encrypted data
    }

    public function retrieveSecureData($encryptedData)
    {
        try {
            $decrypted = decrypt($encryptedData);
            return json_decode($decrypted, true);
        } catch (Exception $e) {
            error_log("Decryption failed: " . $e->getMessage());
            return null;
        }
    }
}
```

## Troubleshooting

### Common Issues

1. **Service Not Found**
   - Ensure services are properly registered in DI container
   - Check namespace imports

2. **Configuration Not Loading**
   - Verify .env file exists and is readable
   - Check environment variable names

3. **Email Not Sending**
   - Verify email provider configuration
   - Check MailHog is running for development

4. **Pusher Connection Issues**
   - Verify Pusher credentials
   - Check network connectivity
   - Ensure Pusher package is installed

### Debug Mode

Enable debug mode for detailed logging:

```php
<?php

use function App\Core\Helpers\{env, dd};

if (env('APP_DEBUG')) {
    // Debug configuration
    dd([
        'email_provider' => config('email.provider'),
        'pusher_ready' => (new PusherService())->isReady(),
        'environment' => env('APP_ENV')
    ]);
}
```

## Contributing

When extending Core components:

1. **Follow Existing Patterns**: Maintain consistency with existing code
2. **Add Documentation**: Update README files with new features
3. **Include Examples**: Provide usage examples for new functionality
4. **Write Tests**: Add unit tests for new components
5. **Handle Errors**: Implement proper error handling and logging

## Related Documentation

- [Main Project README](../../README.md)
- [Features TODO List](../../_docs/FEATURES_TODO.md)
- [API Documentation](API/README.md)
- [Collection Documentation](Collection/README.md)
- [Email Documentation](Email/README.md)
- [Environment Documentation](Env/README.md)
- [Helpers Documentation](Helpers/README.md)
- [Pusher Documentation](Pusher/README.md)

---

The Core framework provides a solid foundation for building modern web applications with Phalcon. Each component is designed to be modular, well-documented, and easy to extend, making it simple to build robust applications quickly and efficiently.