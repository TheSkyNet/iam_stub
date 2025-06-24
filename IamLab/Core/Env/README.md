# Environment Management

The Environment Management component provides functionality for loading and managing environment variables from `.env` files in the Phalcon stub project. It ensures that configuration values are properly loaded and available throughout the application.

## Overview

This folder contains:

- **Env.php** - Environment variable loader class

## Components

### Env Class

**File:** `Env.php`  
**Namespace:** `IamLab\Core\Env`

The `Env` class is responsible for loading environment variables from `.env` files and making them available to the application through PHP's environment variable functions.

#### Key Features

- Loads environment variables from `.env` files
- Validates file existence and readability
- Parses key-value pairs with proper formatting
- Prevents overwriting existing environment variables
- Supports comments in `.env` files
- Error handling for file access issues

#### Methods

##### `__construct(string $path)`
Creates a new Env instance with the specified `.env` file path.

**Parameters:**
- `$path` - Full path to the `.env` file

**Throws:**
- `InvalidArgumentException` - If the file does not exist

```php
use IamLab\Core\Env\Env;

// Load environment from .env file
$env = new Env('.env');
```

##### `load(): void`
Loads environment variables from the `.env` file into PHP's environment.

**Throws:**
- `RuntimeException` - If the file is not readable

```php
$env = new Env('.env');
$env->load();

// Now environment variables are available
$dbHost = getenv('DB_HOST');
$appName = $_ENV['APP_NAME'];
```

## Usage Examples

### Basic Environment Loading

```php
<?php

use IamLab\Core\Env\Env;

try {
    // Load environment variables
    $env = new Env('.env');
    $env->load();
    
    // Access environment variables
    $appName = getenv('APP_NAME');
    $dbHost = getenv('DB_HOST');
    $debug = getenv('APP_DEBUG') === 'true';
    
    echo "Application: {$appName}\n";
    echo "Database Host: {$dbHost}\n";
    echo "Debug Mode: " . ($debug ? 'Enabled' : 'Disabled') . "\n";
    
} catch (InvalidArgumentException $e) {
    echo "Environment file not found: " . $e->getMessage();
} catch (RuntimeException $e) {
    echo "Cannot read environment file: " . $e->getMessage();
}
```

### Loading Different Environment Files

```php
// Load different environment files based on context
$environment = $_SERVER['APP_ENV'] ?? 'local';

$envFiles = [
    'local' => '.env.local',
    'testing' => '.env.testing',
    'staging' => '.env.staging',
    'production' => '.env'
];

$envFile = $envFiles[$environment] ?? '.env';

if (file_exists($envFile)) {
    $env = new Env($envFile);
    $env->load();
    echo "Loaded environment: {$environment}\n";
} else {
    echo "Environment file not found: {$envFile}\n";
}
```

### Helper Function Integration

The project includes a helper function for convenient environment loading:

```php
use function App\Core\Helpers\loadEnv;

// Load environment using helper function
loadEnv('.env');

// Access variables
$config = [
    'app_name' => getenv('APP_NAME'),
    'app_url' => getenv('APP_URL'),
    'database' => [
        'host' => getenv('DB_HOST'),
        'username' => getenv('DB_USERNAME'),
        'password' => getenv('DB_PASSWORD'),
        'database' => getenv('DB_DATABASE')
    ]
];
```

### Environment Variable Validation

```php
function validateEnvironment() {
    $required = [
        'APP_NAME',
        'APP_URL',
        'DB_HOST',
        'DB_USERNAME',
        'DB_PASSWORD',
        'DB_DATABASE'
    ];
    
    $missing = [];
    
    foreach ($required as $var) {
        if (!getenv($var)) {
            $missing[] = $var;
        }
    }
    
    if (!empty($missing)) {
        throw new RuntimeException(
            'Missing required environment variables: ' . implode(', ', $missing)
        );
    }
    
    return true;
}

// Load environment and validate
$env = new Env('.env');
$env->load();
validateEnvironment();
```

## Environment File Format

The `.env` file should follow this format:

```env
# Application Configuration
APP_NAME=MyApplication
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8080

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=myapp
DB_USERNAME=root
DB_PASSWORD=secret

# Email Configuration
MAIL_PROVIDER=mailhog
MAIL_FROM_EMAIL=noreply@example.com
MAIL_FROM_NAME="My Application"

# Cache Configuration
CACHE_DRIVER=file
CACHE_PREFIX=myapp_

# Session Configuration
SESSION_LIFETIME=120
SESSION_DRIVER=file
```

### Format Rules

1. **Key-Value Pairs**: Use `KEY=value` format
2. **Comments**: Lines starting with `#` are ignored
3. **No Spaces**: Avoid spaces around the `=` sign
4. **Quotes**: Use quotes for values with spaces
5. **Empty Lines**: Empty lines are ignored
6. **No Overwriting**: Existing environment variables won't be overwritten

## Integration with Application

### Service Registration

The environment loading is typically done early in the application bootstrap:

```php
// In services.php or bootstrap file
use function App\Core\Helpers\loadEnv;

// Load environment variables before configuration
loadEnv('.env');

// Now configuration can access environment variables
$di->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});
```

### Configuration Integration

Environment variables are commonly used in configuration files:

```php
// In config/config.php
use function App\Core\Helpers\env;

return new Config([
    'app' => [
        'name' => env('APP_NAME', 'Phalcon App'),
        'env' => env('APP_ENV', 'production'),
        'debug' => env('APP_DEBUG', false),
        'url' => env('APP_URL', 'http://localhost'),
    ],
    'database' => [
        'adapter' => 'Mysql',
        'host' => env('DB_HOST', 'localhost'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'dbname' => env('DB_DATABASE', 'phalcon'),
    ]
]);
```

## Best Practices

### 1. Environment File Security
Never commit sensitive environment files to version control:

```gitignore
# Environment files
.env
.env.local
.env.staging
.env.production
.env.*.local

# But keep the example
!.env.example
```

### 2. Environment File Templates
Create `.env.example` files as templates:

```env
# .env.example
APP_NAME=YourAppName
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

MAIL_PROVIDER=mailhog
MAIL_FROM_EMAIL=noreply@example.com
```

### 3. Environment Validation
Always validate critical environment variables:

```php
class EnvironmentValidator
{
    private static $required = [
        'APP_NAME',
        'APP_ENV',
        'DB_HOST',
        'DB_DATABASE',
        'DB_USERNAME'
    ];
    
    public static function validate(): void
    {
        foreach (self::$required as $var) {
            if (empty(getenv($var))) {
                throw new RuntimeException("Required environment variable '{$var}' is not set");
            }
        }
    }
    
    public static function validateEmail(): void
    {
        $provider = getenv('MAIL_PROVIDER');
        
        if ($provider === 'resend' && empty(getenv('RESEND_API_KEY'))) {
            throw new RuntimeException("RESEND_API_KEY is required when using Resend provider");
        }
    }
}
```

### 4. Environment-Specific Loading
Load different configurations based on environment:

```php
function loadEnvironmentConfig() {
    $env = getenv('APP_ENV') ?: 'local';
    
    $envFiles = [
        ".env.{$env}.local",
        ".env.{$env}",
        '.env.local',
        '.env'
    ];
    
    foreach ($envFiles as $file) {
        if (file_exists($file)) {
            $envLoader = new Env($file);
            $envLoader->load();
            break;
        }
    }
}
```

## Error Handling

### Common Issues and Solutions

#### 1. File Not Found
```php
try {
    $env = new Env('.env');
    $env->load();
} catch (InvalidArgumentException $e) {
    // Handle missing .env file
    if (file_exists('.env.example')) {
        echo "Please copy .env.example to .env and configure your settings\n";
    } else {
        echo "Environment file not found: " . $e->getMessage() . "\n";
    }
}
```

#### 2. File Not Readable
```php
try {
    $env = new Env('.env');
    $env->load();
} catch (RuntimeException $e) {
    // Handle permission issues
    echo "Cannot read .env file. Check file permissions.\n";
    echo "Error: " . $e->getMessage() . "\n";
}
```

#### 3. Malformed Environment File
```php
function validateEnvFile($path) {
    if (!file_exists($path)) {
        throw new InvalidArgumentException("Environment file not found: {$path}");
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $lineNumber = 0;
    
    foreach ($lines as $line) {
        $lineNumber++;
        $line = trim($line);
        
        // Skip comments and empty lines
        if (empty($line) || str_starts_with($line, '#')) {
            continue;
        }
        
        // Check for valid key=value format
        if (!str_contains($line, '=')) {
            throw new RuntimeException("Invalid format on line {$lineNumber}: {$line}");
        }
    }
    
    return true;
}
```

## Testing Environment Loading

### Unit Tests

```php
class EnvTest extends \PHPUnit\Framework\TestCase
{
    private $tempEnvFile;
    
    protected function setUp(): void
    {
        $this->tempEnvFile = tempnam(sys_get_temp_dir(), 'test_env_');
    }
    
    protected function tearDown(): void
    {
        if (file_exists($this->tempEnvFile)) {
            unlink($this->tempEnvFile);
        }
    }
    
    public function testLoadEnvironmentVariables()
    {
        // Create test .env file
        file_put_contents($this->tempEnvFile, "TEST_VAR=test_value\nANOTHER_VAR=another_value");
        
        // Load environment
        $env = new Env($this->tempEnvFile);
        $env->load();
        
        // Assert variables are loaded
        $this->assertEquals('test_value', getenv('TEST_VAR'));
        $this->assertEquals('another_value', getenv('ANOTHER_VAR'));
    }
    
    public function testFileNotFound()
    {
        $this->expectException(InvalidArgumentException::class);
        new Env('/nonexistent/file.env');
    }
    
    public function testCommentsAreIgnored()
    {
        file_put_contents($this->tempEnvFile, "# This is a comment\nTEST_VAR=value\n# Another comment");
        
        $env = new Env($this->tempEnvFile);
        $env->load();
        
        $this->assertEquals('value', getenv('TEST_VAR'));
    }
}
```

## Performance Considerations

1. **Load Once**: Environment variables should be loaded once during application bootstrap
2. **File Size**: Keep `.env` files reasonably small for faster parsing
3. **Caching**: Consider caching parsed environment variables for high-traffic applications
4. **Memory Usage**: Environment variables are stored in memory, so avoid excessive variables

## Security Considerations

1. **File Permissions**: Ensure `.env` files have appropriate permissions (600 or 644)
2. **Version Control**: Never commit `.env` files containing sensitive data
3. **Backup Security**: Secure backups of environment files
4. **Access Logging**: Log access to sensitive environment variables
5. **Encryption**: Consider encrypting sensitive values in environment files

## Future Enhancements

The environment management system could be enhanced with:

- Environment variable encryption/decryption
- Remote environment variable loading (from APIs, databases)
- Environment variable validation schemas
- Hot-reloading of environment variables
- Environment variable inheritance and overrides
- Integration with secret management systems (HashiCorp Vault, AWS Secrets Manager)

## Dependencies

- PHP built-in functions (`file_exists`, `file`, `getenv`, `putenv`)
- Standard PHP exceptions (`InvalidArgumentException`, `RuntimeException`)

## Related Documentation

- [Helpers Documentation](../Helpers/README.md) - For the `loadEnv()` and `env()` helper functions
- [Main Project README](../../../README.md)
- [PHP Environment Variables Documentation](https://www.php.net/manual/en/function.getenv.php)