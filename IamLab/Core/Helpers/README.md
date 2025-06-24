# Helper Functions

The Helper Functions provide a collection of utility functions that simplify common tasks throughout the Phalcon stub project. These functions offer convenient shortcuts for configuration access, dependency injection, data manipulation, encryption, and more.

## Overview

This folder contains utility functions organized into several files:

- **util.php** - Core utility functions (config, DI, environment, email, debugging)
- **array.php** - Array and object manipulation functions
- **crypt.php** - Encryption and decryption functions
- **include.php** - Autoloader for all helper functions

## Components

### Core Utilities (util.php)

**Namespace:** `App\Core\Helpers`

#### Configuration Functions

##### `config($key, $default = null)`
Retrieves configuration values using dot notation.

**Parameters:**
- `$key` - Configuration key using dot notation (e.g., 'database.host')
- `$default` - Default value if key is not found

**Returns:** Mixed - Configuration value or default

```php
use function App\Core\Helpers\config;

// Get database configuration
$dbHost = config('database.host', 'localhost');
$dbConfig = config('database'); // Returns entire database config array

// Get nested configuration
$emailProvider = config('email.provider', 'mailhog');
$pusherCluster = config('pusher.cluster', 'mt1');
```

##### `env($key, $default = null)`
Retrieves environment variables with optional default values.

**Parameters:**
- `$key` - Environment variable name
- `$default` - Default value if variable is not set

**Returns:** String|null - Environment variable value or default

```php
use function App\Core\Helpers\env;

// Get environment variables
$appName = env('APP_NAME', 'Phalcon App');
$debug = env('APP_DEBUG', false);
$dbPassword = env('DB_PASSWORD');

// Use in configuration
$config = [
    'app_name' => env('APP_NAME'),
    'app_url' => env('APP_URL', 'http://localhost'),
    'database' => [
        'host' => env('DB_HOST', 'localhost'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
    ]
];
```

#### Dependency Injection Functions

##### `di($service)`
Retrieves services from the dependency injection container.

**Parameters:**
- `$service` - Service name

**Returns:** Mixed - Service instance

```php
use function App\Core\Helpers\di;

// Get services from DI container
$db = di('db');
$config = di('config');
$session = di('session');
$authService = di('authService');

// Use in functions
function getCurrentUser() {
    $authService = di('authService');
    return $authService->getUser();
}
```

#### Environment Functions

##### `loadEnv($path = '')`
Loads environment variables from a .env file.

**Parameters:**
- `$path` - Path to the .env file

```php
use function App\Core\Helpers\loadEnv;

// Load environment variables
loadEnv('.env');
loadEnv('.env.local');

// Load different environments
$environment = $_SERVER['APP_ENV'] ?? 'local';
loadEnv(".env.{$environment}");
```

#### Email Functions

##### `email(string $to, string $subject, string $body, array $options = []): bool`
Sends an email using the configured email service.

**Parameters:**
- `$to` - Recipient email address
- `$subject` - Email subject
- `$body` - Email body (HTML or plain text)
- `$options` - Additional options (is_html, from_name, etc.)

**Returns:** bool - True if email was sent successfully

```php
use function App\Core\Helpers\email;

// Send simple email
$result = email(
    'user@example.com',
    'Welcome!',
    'Thank you for joining our platform.'
);

// Send HTML email with options
$result = email(
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
    echo "Failed to send email.";
}
```

#### File System Functions

##### `moveTo(string $disk, string $from, string $to)`
Moves files between locations using the specified disk.

**Parameters:**
- `$disk` - Disk service name
- `$from` - Source file path
- `$to` - Destination file path

**Returns:** Mixed - Result of move operation

```php
use function App\Core\Helpers\moveTo;

// Move file using file disk
$result = moveTo('file', '/tmp/upload.jpg', '/images/profile.jpg');

// Move using different disks
moveTo('tmp', 'temp_file.pdf', 'processed_file.pdf');
```

#### Debugging Functions

##### `dd(...$variable)`
Dumps variables and dies (debug and die).

**Parameters:**
- `...$variable` - Variables to dump

```php
use function App\Core\Helpers\dd;

// Debug single variable
$user = User::findFirst();
dd($user);

// Debug multiple variables
dd($user, $config, $request);

// Debug in development
if (env('APP_DEBUG')) {
    dd($debugData);
}
```

### Array and Object Manipulation (array.php)

**Namespace:** `App\Core\Helpers`

#### Type Casting Functions

##### `cast($value, $cast)`
Casts a value to a specific type.

**Parameters:**
- `$value` - Value to cast
- `$cast` - Target type ('int', 'float', 'bool', 'string')

**Returns:** Mixed - Casted value

```php
use function App\Core\Helpers\cast;

// Cast values
$id = cast('123', 'int');        // 123 (integer)
$price = cast('19.99', 'float'); // 19.99 (float)
$active = cast('1', 'bool');     // true (boolean)
$name = cast(123, 'string');     // '123' (string)

// Use in API parameter handling
function getParam($name, $default = null, $cast = null) {
    $value = $_POST[$name] ?? $default;
    return $cast ? cast($value, $cast) : $value;
}

$userId = getParam('user_id', 0, 'int');
$isActive = getParam('active', false, 'bool');
```

#### Object Manipulation Functions

##### `merge_objects(...$objects)`
Merges multiple objects into a new object.

**Parameters:**
- `...$objects` - Objects to merge

**Returns:** Object - New merged object

```php
use function App\Core\Helpers\merge_objects;

// Merge stdClass objects
$obj1 = (object)['name' => 'John', 'age' => 25];
$obj2 = (object)['email' => 'john@example.com', 'age' => 26];
$obj3 = (object)['city' => 'New York'];

$merged = merge_objects($obj1, $obj2, $obj3);
// Result: {name: 'John', age: 26, email: 'john@example.com', city: 'New York'}

// Merge custom objects
class User {
    public $name;
    public $email;
}

$user1 = new User();
$user1->name = 'John';

$user2 = new User();
$user2->email = 'john@example.com';

$mergedUser = merge_objects($user1, $user2);
```

#### Array Functions

##### `splat(...$args): array`
Converts arguments to an array (splat operator helper).

**Parameters:**
- `...$args` - Arguments to convert

**Returns:** array - Array of arguments

```php
use function App\Core\Helpers\splat;

// Convert arguments to array
$array = splat('a', 'b', 'c', 'd');
// Result: ['a', 'b', 'c', 'd']

// Use in function calls
function processItems(...$items) {
    $itemArray = splat(...$items);
    return array_map('strtoupper', $itemArray);
}

$result = processItems('apple', 'banana', 'cherry');
```

##### `concatenate($transform, ...$strings)`
Concatenates strings and applies a transformation function.

**Parameters:**
- `$transform` - Transformation function
- `...$strings` - Strings to concatenate

**Returns:** Mixed - Result of transformation

```php
use function App\Core\Helpers\concatenate;

// Concatenate and transform
$result = concatenate('strtoupper', 'hello', ' ', 'world');
// Result: 'HELLO WORLD'

$result = concatenate('md5', 'user', '123', 'salt');
// Result: MD5 hash of 'user123salt'

// Use with custom functions
$result = concatenate(
    fn($str) => str_replace(' ', '-', strtolower($str)),
    'My', ' ', 'Blog', ' ', 'Post'
);
// Result: 'my-blog-post'
```

#### Collection Functions

##### `collect($collection): Collection`
Creates a new Collection instance from an array or iterable.

**Parameters:**
- `$collection` - Array or iterable to convert

**Returns:** Collection - Enhanced collection instance

```php
use function App\Core\Helpers\collect;

// Create collection from array
$numbers = collect([1, 2, 3, 4, 5]);

// Chain collection methods
$result = collect([1, 2, 3, 4, 5])
    ->map(fn($n) => $n * 2)
    ->filter(fn($n) => $n > 5)
    ->each(fn($n) => echo $n . ' ');

// Work with model data
$users = User::find();
$userCollection = collect($users->toArray())
    ->map(fn($user) => [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email']
    ]);
```

### Encryption Functions (crypt.php)

**Namespace:** `App\Core\Helpers`

#### Encryption Functions

##### `crypt(string $message): string`
Encrypts a message using the application's encryption key.

**Parameters:**
- `$message` - Message to encrypt

**Returns:** string - Encrypted message

**Throws:** Exception - If encryption fails

```php
use function App\Core\Helpers\crypt;

// Encrypt sensitive data
$sensitiveData = 'user-secret-token';
$encrypted = crypt($sensitiveData);

// Store encrypted data
$user = new User();
$user->setEncryptedToken($encrypted);
$user->save();

// Encrypt configuration values
$apiKey = crypt(env('THIRD_PARTY_API_KEY'));
```

##### `decrypt(string $encrypted): string`
Decrypts a message that was encrypted with the `crypt()` function.

**Parameters:**
- `$encrypted` - Encrypted message

**Returns:** string - Decrypted message

**Throws:** 
- `BadFormatException` - If the encrypted data is malformed
- `EnvironmentIsBrokenException` - If the crypto environment is broken
- `WrongKeyOrModifiedCiphertextException` - If the key is wrong or data is modified

```php
use function App\Core\Helpers\decrypt;

// Decrypt data
$user = User::findFirst();
$encryptedToken = $user->getEncryptedToken();

try {
    $decryptedToken = decrypt($encryptedToken);
    // Use decrypted token
} catch (Exception $e) {
    // Handle decryption error
    error_log("Decryption failed: " . $e->getMessage());
}

// Decrypt configuration values
try {
    $apiKey = decrypt($encryptedApiKey);
    $apiClient = new ThirdPartyApi($apiKey);
} catch (Exception $e) {
    throw new RuntimeException("Failed to decrypt API key");
}
```

## Usage Examples

### Configuration Management

```php
<?php

use function App\Core\Helpers\{config, env, loadEnv};

// Load environment and get configuration
loadEnv('.env');

// Database configuration
$dbConfig = [
    'host' => env('DB_HOST', 'localhost'),
    'port' => env('DB_PORT', 3306),
    'database' => env('DB_DATABASE'),
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
];

// Application configuration
$appConfig = [
    'name' => env('APP_NAME', 'Phalcon App'),
    'url' => env('APP_URL', 'http://localhost'),
    'debug' => env('APP_DEBUG', false),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
];

// Use configuration in services
function createDatabaseConnection() {
    return new PDO(
        sprintf('mysql:host=%s;dbname=%s', 
            config('database.host'), 
            config('database.dbname')
        ),
        config('database.username'),
        config('database.password')
    );
}
```

### Data Processing Pipeline

```php
use function App\Core\Helpers\{collect, cast, merge_objects};

// Process user data
function processUserData($rawData) {
    return collect($rawData)
        ->map(function($user) {
            // Cast data types
            $user['id'] = cast($user['id'], 'int');
            $user['is_active'] = cast($user['is_active'], 'bool');
            $user['balance'] = cast($user['balance'], 'float');
            
            // Add computed fields
            $computed = (object)[
                'full_name' => $user['first_name'] . ' ' . $user['last_name'],
                'display_email' => strtolower($user['email']),
                'account_status' => $user['is_active'] ? 'Active' : 'Inactive'
            ];
            
            // Merge original data with computed fields
            return merge_objects((object)$user, $computed);
        })
        ->filter(fn($user) => $user->is_active)
        ->each(fn($user) => echo "Processing: {$user->full_name}\n");
}
```

### Email Notification System

```php
use function App\Core\Helpers\{email, config, env};

class NotificationService 
{
    public function sendWelcomeEmail($user) {
        $appName = config('app.name');
        $supportEmail = env('SUPPORT_EMAIL', 'support@example.com');
        
        $subject = "Welcome to {$appName}!";
        $body = "
            <h1>Welcome, {$user->getName()}!</h1>
            <p>Thank you for joining {$appName}.</p>
            <p>If you have any questions, contact us at {$supportEmail}</p>
        ";
        
        return email(
            $user->getEmail(),
            $subject,
            $body,
            [
                'is_html' => true,
                'from_name' => $appName,
                'reply_to' => $supportEmail
            ]
        );
    }
    
    public function sendPasswordResetEmail($user, $resetToken) {
        $resetUrl = config('app.url') . "/reset-password?token={$resetToken}";
        
        $body = "
            <h2>Password Reset Request</h2>
            <p>Click the link below to reset your password:</p>
            <p><a href=\"{$resetUrl}\">Reset Password</a></p>
            <p>This link expires in 1 hour.</p>
        ";
        
        return email(
            $user->getEmail(),
            'Password Reset Request',
            $body,
            ['is_html' => true]
        );
    }
}
```

### Secure Data Handling

```php
use function App\Core\Helpers\{crypt, decrypt, env};

class SecureDataManager 
{
    public function storeSecureData($userId, $sensitiveData) {
        try {
            $encrypted = crypt($sensitiveData);
            
            // Store encrypted data in database
            $secureRecord = new SecureData();
            $secureRecord->setUserId($userId);
            $secureRecord->setEncryptedData($encrypted);
            $secureRecord->setCreatedAt(date('Y-m-d H:i:s'));
            
            return $secureRecord->save();
        } catch (Exception $e) {
            error_log("Failed to encrypt data: " . $e->getMessage());
            return false;
        }
    }
    
    public function retrieveSecureData($userId) {
        $record = SecureData::findFirst([
            'conditions' => 'user_id = :userId:',
            'bind' => ['userId' => $userId]
        ]);
        
        if (!$record) {
            return null;
        }
        
        try {
            return decrypt($record->getEncryptedData());
        } catch (Exception $e) {
            error_log("Failed to decrypt data: " . $e->getMessage());
            return null;
        }
    }
}
```

### Development Debugging

```php
use function App\Core\Helpers\{dd, env, config};

// Debug configuration
if (env('APP_DEBUG')) {
    dd([
        'environment' => env('APP_ENV'),
        'database' => config('database'),
        'email' => config('email'),
        'session' => $_SESSION ?? 'No session'
    ]);
}

// Debug user data
function debugUser($userId) {
    if (!env('APP_DEBUG')) {
        return;
    }
    
    $user = User::findFirst($userId);
    $permissions = $user->getPermissions();
    $lastLogin = $user->getLastLogin();
    
    dd(compact('user', 'permissions', 'lastLogin'));
}

// Debug API requests
function debugApiRequest($request, $response) {
    if (env('APP_ENV') === 'local') {
        dd([
            'method' => $request->getMethod(),
            'uri' => $request->getURI(),
            'headers' => $request->getHeaders(),
            'body' => $request->getRawBody(),
            'response' => $response
        ]);
    }
}
```

## Best Practices

### 1. Environment Configuration
Use environment variables for all configuration:

```php
// Good: Environment-based configuration
$config = [
    'database' => [
        'host' => env('DB_HOST'),
        'username' => env('DB_USERNAME'),
        'password' => env('DB_PASSWORD'),
    ]
];

// Avoid: Hard-coded values
$config = [
    'database' => [
        'host' => 'localhost',
        'username' => 'root',
        'password' => 'secret',
    ]
];
```

### 2. Type Safety
Always cast values when needed:

```php
// Good: Explicit type casting
$userId = cast($request->getParam('user_id'), 'int');
$isActive = cast($request->getParam('active'), 'bool');

// Avoid: Assuming types
$userId = $request->getParam('user_id');
$isActive = $request->getParam('active');
```

### 3. Error Handling
Handle encryption/decryption errors gracefully:

```php
// Good: Proper error handling
try {
    $decrypted = decrypt($encryptedData);
    return $decrypted;
} catch (Exception $e) {
    error_log("Decryption failed: " . $e->getMessage());
    return null;
}

// Avoid: Unhandled exceptions
$decrypted = decrypt($encryptedData); // May throw exception
```

### 4. Debug Safety
Only use debug functions in development:

```php
// Good: Environment-aware debugging
if (env('APP_DEBUG')) {
    dd($debugData);
}

// Avoid: Always debugging
dd($debugData); // Will expose data in production
```

## Security Considerations

1. **Encryption Keys**: Ensure encryption keys are properly configured and secure
2. **Environment Variables**: Never commit sensitive environment variables to version control
3. **Debug Functions**: Never use debug functions in production environments
4. **Input Validation**: Always validate and cast input data
5. **Error Logging**: Log errors securely without exposing sensitive information

## Performance Considerations

1. **Configuration Caching**: Cache frequently accessed configuration values
2. **Collection Usage**: Use collections efficiently for large datasets
3. **Encryption Overhead**: Be aware of encryption/decryption performance costs
4. **Memory Usage**: Monitor memory usage when processing large collections

## Dependencies

- **Phalcon Framework** - For dependency injection and configuration
- **Defuse\Crypto** - For encryption/decryption functions
- **IamLab\Core\Collection** - For collection functionality
- **IamLab\Core\Env** - For environment loading
- **IamLab\Core\Email** - For email functionality

## Related Documentation

- [API Documentation](../API/README.md)
- [Collection Documentation](../Collection/README.md)
- [Email Documentation](../Email/README.md)
- [Environment Documentation](../Env/README.md)
- [Main Project README](../../../README.md)