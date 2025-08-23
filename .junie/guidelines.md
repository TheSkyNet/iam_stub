# Development Guidelines

This document provides essential development information for the Phalcon-based IamLab project. This is intended for advanced developers and focuses on project-specific configurations and processes.

## 🚨 Important Development Notes

**READ THIS FIRST** - Essential guidelines for all developers working on this project:

### 🎨 Frontend Framework
- **Use DaisyUI Components**: This project uses **DaisyUI** with Tailwind CSS for UI components
- **Phalcon + DaisyUI Integration**: Leverage DaisyUI's component library for consistent styling
- **Component Documentation**: Reference [DaisyUI Components](https://daisyui.com/components/) for available UI elements

### 🔍 Code Reuse Philosophy
- **Look for Existing Code FIRST**: Before creating new functionality, search the codebase for existing implementations
- **Extend, Don't Duplicate**: Use existing services, components, and patterns as foundation
- **Check Pre-Made Services**: Review the [Current Pre-Made Services](#current-pre-made-services) section below

### 🏗️ SOLID PHP Principles
- **Single Responsibility**: Each class should have one reason to change
- **Open/Closed**: Open for extension, closed for modification
- **Liskov Substitution**: Derived classes must be substitutable for base classes
- **Interface Segregation**: Many client-specific interfaces are better than one general-purpose interface
- **Dependency Inversion**: Depend on abstractions, not concretions

### 📋 Development Checklist
Before writing new code, ask yourself:
- [ ] Does similar functionality already exist in the codebase?
- [ ] Can I extend an existing service instead of creating a new one?
- [ ] Am I using DaisyUI components for UI elements?
- [ ] Does my code follow SOLID principles?
- [ ] Am I extending the `aAPI` base class for new API endpoints?

## Build/Configuration Instructions

### Environment Setup

1. **Docker Environment**: The project uses Docker with a custom `./phalcons` script (similar to Laravel Sail)
   ```bash
   ./phalcons up -d    # Start containers in background
   ./phalcons stop     # Stop containers
   ./phalcons ps       # Check container status
   ```

2. **Dependencies Installation**:
   ```bash
   ./phalcons composer install    # PHP dependencies
   ./phalcons npm install         # Node.js dependencies
   ```

3. **Environment Configuration**:
   - Copy `.env.example` to `.env` and configure database/service settings
   - Key environment variables:
     - `APP_PORT` (default: 80)
     - `DB_*` variables for MySQL configuration
     - `LMS_*` variables for LMS integrations (Ollama, Gemini, Tencent EDU)

### Frontend Build Process

The project uses **Laravel Mix** with Tailwind CSS:

```bash
# Development builds
./phalcons npm run dev          # Single build
./phalcons npm run watch        # Watch for changes
./phalcons npm run hot          # Hot module replacement

# Production build
./phalcons npm run prod
```

**Build Configuration**:
- Source: `assets/js/app.js` → `public/js/app.js`
- Styles: `assets/scss/main.scss` → `public/css/app.css`
- Uses Tailwind CSS with DaisyUI components
- Includes Autoprefixer and source maps

### Database Setup

```bash
./phalcons migrate              # Run migrations
./phalcons migrate:generate     # Generate new migrations
./phalcons migrate:list         # List all migrations
./phalcons migrate:seed         # Run database seeders
```

**Migration Configuration**: Uses Phalcon Migrations with config at `./bin/migrations.php`

## Testing Information

### PHPUnit Configuration

- **Version**: PHPUnit 10.5.x
- **Configuration**: `phpunit.xml` in project root
- **Bootstrap**: `tests/bootstrap.php`

### Test Suites

The project has three main test suites:

1. **Unit Tests**: `tests/Unit/` - Isolated component testing
2. **Feature Tests**: `tests/Feature/` - Integration testing
3. **LMS Tests**: `tests/Unit/Service/LMS/` and `tests/Feature/LMS/` - LMS-specific testing

### Running Tests

```bash
# Using Docker (requires containers to be running)
./phalcons test                           # All tests
./phalcons test tests/Unit/               # Unit tests only
./phalcons test --filter=TestMethodName   # Specific test method

# Direct PHPUnit (without Docker)
./vendor/bin/phpunit                      # All tests
./vendor/bin/phpunit tests/Unit/          # Unit tests only
./vendor/bin/phpunit tests/Unit/SimpleExampleTest.php  # Specific test file
```

### Adding New Tests

1. **Unit Tests**: Place in `tests/Unit/` with namespace `Tests\Unit`
2. **Feature Tests**: Place in `tests/Feature/` with namespace `Tests\Feature`
3. **Test Structure**:
   ```php
   <?php
   namespace Tests\Unit;
   use PHPUnit\Framework\TestCase;
   
   class YourTest extends TestCase
   {
       public function testSomething(): void
       {
           $this->assertTrue(true);
       }
   }
   ```

### Test Environment Variables

Key testing environment variables (configured in `phpunit.xml`):
- `APP_ENV=testing`
- `LMS_OLLAMA_ENABLED=true`
- `LMS_OLLAMA_HOST=http://ollama:11434`
- `LMS_GEMINI_ENABLED=false`

## Phalcons Commands Reference

The `./phalcons` script provides comprehensive Docker-based development tools:

### Container Management
```bash
./phalcons up -d              # Start application in background
./phalcons stop               # Stop application
./phalcons restart            # Restart application
./phalcons ps                 # Show container status
./phalcons build --no-cache   # Rebuild containers
```

### Development Tools
```bash
./phalcons shell              # Enter application container shell
./phalcons root-shell         # Enter as root user
./phalcons php -v             # Run PHP commands
./phalcons composer install   # Composer commands
./phalcons npm run dev        # NPM commands
```

### Database Operations
```bash
# MySQL Commands (AI-Friendly Non-Interactive)
./phalcons mysql:query "SHOW TABLES;"           # Execute single SQL query (recommended for AI)
./phalcons mysql:file database/seeds/data.sql   # Execute SQL file (recommended for AI)
./phalcons mysql:dump backup.sql                # Create database dump (recommended for AI)

# MySQL Interactive Session (problematic for AI)
./phalcons mysql              # Interactive MySQL CLI - AI cannot exit this session

# Migration Commands
./phalcons migrate            # Run migrations
./phalcons migrate:generate   # Generate migrations
./phalcons migrate:list       # List migrations
./phalcons migrate:seed       # Run seeders
```

### Custom Commands
```bash
./phalcons command list       # List available custom commands
./phalcons command test:mail user@example.com -v  # Example custom command
```

### Service Management
```bash
./phalcons service:install    # Install as system service
./phalcons service:start      # Start service
./phalcons service:stop       # Stop service
./phalcons service:status     # Check service status
./phalcons service:uninstall  # Remove service
```

### Debugging
```bash
./phalcons debug queue:work   # Run commands with Xdebug enabled
```

## Additional Development Information

### Project Structure

- **Core Framework**: Phalcon PHP 5.x with custom architecture
- **Frontend**: Laravel Mix + Tailwind CSS + DaisyUI
- **Database**: MySQL 8.0 with Redis for caching
- **Email**: MailHog for development email testing
- **Authentication**: JWT-based with OAuth integrations

### Key Dependencies

**PHP Dependencies**:
- `phalcon/devtools` - Development tools
- `phalcon/migrations` - Database migrations
- `firebase/php-jwt` - JWT authentication
- `phpunit/phpunit` - Testing framework
- `pusher/pusher-php-server` - Real-time features

**JavaScript Dependencies**:
- `laravel-mix` - Asset compilation
- `tailwindcss` + `daisyui` - CSS framework
- `pusher-js` - Real-time client
- `mithril` - Frontend framework

### Configuration Files

- `IamLab/config/config.php` - Main Phalcon configuration
- `IamLab/config/services.php` - Dependency injection services
- `composer.json` - PHP dependencies and autoloading
- `package.json` - Node.js dependencies and build scripts
- `webpack.mix.js` - Frontend build configuration
- `tailwind.config.js` - Tailwind CSS configuration

### Development Workflow

1. **Start Development Environment**:
   ```bash
   ./phalcons up -d
   ./phalcons migrate
   ./phalcons npm run watch
   ```

2. **Code Changes**: Edit files in `IamLab/` (PHP) or `assets/` (frontend)

3. **Testing**: Run tests frequently during development
   ```bash
   ./phalcons test
   ```

4. **Database Changes**: Generate and run migrations
   ```bash
   ./phalcons migrate:generate --table=your_table
   ./phalcons migrate
   ```

### Code Style Guidelines

- **PHP**: Follow PSR-12 coding standards
- **Namespacing**: Use `IamLab\` namespace for application code
- **Testing**: Write tests for new features and bug fixes
- **Documentation**: Update relevant documentation in `_docs/` directory

### Debugging Tips

- Use `./phalcons debug` for Xdebug-enabled commands
- Check logs in Docker containers: `./phalcons shell` then view logs
- Use MailHog at `http://localhost:8025` for email debugging
- Redis CLI: `./phalcons shell` then `redis-cli -h redis`

### Performance Considerations

- Use Redis for caching where appropriate
- Optimize database queries and use proper indexing
- Minimize frontend bundle size with proper tree-shaking
- Use Docker multi-stage builds for production deployments

## Current Pre-Made Services

The IamLab project includes several pre-built services that provide comprehensive functionality for authentication, content management, and integrations. Each service is well-documented and can be easily configured or extended.

### 🔐 Authentication Services

#### JWT Authentication Service
- **Location**: `IamLab/Service/Auth/`
- **Documentation**: [`_docs/JWT_AUTHENTICATION.md`](_docs/JWT_AUTHENTICATION.md)
- **Features**:
  - Access token generation (configurable expiry, default: 1 hour)
  - Refresh token generation (configurable expiry, default: 7 days)
  - API key generation and validation
  - Session-based authentication fallback
  - Configurable token expiry times via environment variables
- **API Endpoints**:
  - `POST /auth` - Login with JWT tokens
  - `POST /auth/refresh-token` - Refresh access tokens
  - `POST /auth/generate-api-key` - Generate API keys
  - `GET /auth/profile` - Get user profile
  - `POST /auth/update-profile` - Update user profile
- **Token Expiry Configuration**:
  - `JWT_ACCESS_TOKEN_EXPIRY` - Access token expiry in seconds (default: 3600)
  - `JWT_REFRESH_TOKEN_EXPIRY` - Refresh token expiry in seconds (default: 604800)
  - See documentation for security considerations and recommended values

#### OAuth Integration Service
- **Location**: `IamLab/Service/OAuth.php` and `IamLab/Service/Auth/`
- **Documentation**: [`_docs/OAUTH_INTEGRATION.md`](_docs/OAUTH_INTEGRATION.md)
- **Supported Providers**:
  - Google OAuth 2.0
  - GitHub OAuth
  - Facebook OAuth
  - Generic OAuth 2.0 (custom providers)
- **API Endpoints**:
  - `GET /api/oauth/providers` - Get available providers
  - `GET /api/oauth/redirect?provider=google` - Initiate OAuth flow
  - `GET /api/oauth/callback?provider=google&code=...&state=...` - Handle OAuth callback
  - `POST /api/oauth/unlink?provider=google` - Unlink OAuth provider

### 🎓 LMS Integration Service

#### Learning Management System Service
- **Location**: `IamLab/Service/LMS/`
- **Documentation**: [`_docs/lms-integration-setup.md`](_docs/lms-integration-setup.md)
- **Supported Integrations**:
  - **Google Gemini API** - Advanced AI content generation and analysis
  - **Ollama** - Local LLM for privacy-focused AI processing
  - **Tencent Education Cloud** - Chinese LMS platform with course management
- **Key Features**:
  - Content generation and text analysis
  - Course creation and management
  - Multi-provider fallback support
  - Health checks and status monitoring
- **Management Commands**:
  - `./phalcons command ollama enable` - Enable Ollama service
  - `./phalcons command ollama disable` - Disable Ollama service
  - `./phalcons command ollama status` - Check Ollama status
  - `./phalcons command ollama restart` - Restart Ollama service

### 👥 User Management Services

#### Users API Service
- **Location**: `IamLab/Service/UsersApi.php`
- **Features**: User CRUD operations, profile management
- **Base Class**: Extends `aAPI` for standardized API functionality

#### Roles API Service
- **Location**: `IamLab/Service/RolesApi.php`
- **Documentation**: [`_docs/ROLE_GUARDS_DOCUMENTATION.md`](_docs/ROLE_GUARDS_DOCUMENTATION.md)
- **Features**: Role-based access control, permission management
- **Base Class**: Extends `aAPI` with role validation methods

### 📁 File Management Services

#### Filepond API Service
- **Location**: `IamLab/Service/Filepond/`
- **Features**: File upload, processing, and management
- **Components**:
  - `FilepondApi.php` - API controller
  - `FilepondService.php` - Core service logic
  - `FilepondFile.php` - File handling utilities

### ⚡ Real-time & Background Services

#### Pusher API Service
- **Location**: `IamLab/Service/PusherApi.php`
- **Features**: Real-time notifications and messaging
- **Integration**: Pusher WebSocket service

#### Job Queue Service
- **Location**: `IamLab/Service/JobQueue.php`
- **Features**: Background job processing and queue management
- **Database**: Uses `jobs` table for job persistence
- **API**: `IamLab/Service/JobsApi.php` for job management
- **Worker**: `IamLab/Core/Command/WorkerCommand.php` for processing jobs
- **Documentation**: See [Job Queue System](#job-queue-system) section below

#### Settings Service
- **Location**: `IamLab/Service/SettingsService.php`
- **Features**: Application configuration management

## API Structure and Architecture

### Base API Class (`aAPI`)

All API services extend the abstract `aAPI` class located at `IamLab/Core/API/aAPI.php`. This provides:

#### Core Methods
- `dispatch(mixed $data, int $status)` - Send API responses
- `dispatchError(mixed $data, int $status)` - Send error responses
- `getData()` - Get request data
- `getParam(string $name, mixed $default, string $cast)` - Get request parameters
- `getRouteParam(string $name, mixed $default, string $cast)` - Get route parameters

#### Authentication & Authorization
- `requireAuth()` - Require user authentication
- `requireAdmin()` - Require admin privileges
- `requireRole($roles)` - Require specific roles
- `requireAllRoles(array $roles)` - Require multiple roles
- `getCurrentUser()` - Get current authenticated user

### API Endpoint Patterns

#### Standard REST Patterns
- `GET /api/{service}` - List resources
- `GET /api/{service}/{id}` - Get specific resource
- `POST /api/{service}` - Create new resource
- `PUT /api/{service}/{id}` - Update resource
- `DELETE /api/{service}/{id}` - Delete resource

#### Authentication Endpoints
- `POST /auth` - Login
- `POST /auth/register` - Register
- `GET /auth/profile` - Get profile
- `POST /auth/logout` - Logout

#### Service-Specific Endpoints
- `/api/oauth/*` - OAuth integration endpoints
- `/api/jobs/*` - Job queue management
- `/api/roles/*` - Role management
- `/api/users/*` - User management
- `/filepond/*` - File upload endpoints

### Route Groups and Guards

The project uses a sophisticated routing system with:
- **Route Groups**: Organized by functionality (auth, api, admin)
- **Route Guards**: Role-based access control
- **Documentation**: [`_docs/ROUTE_GROUPS_DOCUMENTATION.md`](_docs/ROUTE_GROUPS_DOCUMENTATION.md)

### Configuration Management

Services are configured through:
- **Environment Variables**: `.env` file for service toggles and API keys
- **Configuration Files**: `IamLab/config/` directory
- **Service Registration**: Automatic service discovery and registration

### Adding New Services

To create a new API service:

1. **Create Service Class**: Extend `aAPI` base class
2. **Implement Methods**: Add your API endpoints
3. **Configure Routes**: Register routes in route groups
4. **Add Documentation**: Document in `_docs/` directory
5. **Generate Components**: Use available generation tools

#### Code Generation Tools

**Phalcon DevTools Generation:**
```bash
# Generate Phalcon components (models, controllers, etc.)
./phalcons phalcon --help                    # List all available generators
./phalcons phalcon create-model Users        # Generate model
./phalcons phalcon create-controller Api     # Generate controller
./phalcons phalcon create-migration users    # Generate migration
```

**Note**: The `./phalcons phalcon` command provides access to Phalcon DevTools for generating models, controllers, migrations, and other Phalcon-specific components. For detailed documentation on all available Phalcon generators, refer to the [Phalcon DevTools Documentation](https://docs.phalcon.io/5.0/en/devtools).

**Frontend Component Generation:**
- Currently, there are no built-in JavaScript/frontend generators
- Frontend components should be created manually following the existing patterns in `assets/js/components/`
- Use existing components as templates for consistency

Example API Service:
```php
<?php
use IamLab\Core\API\aAPI;

class NewServiceApi extends aAPI
{
    public function indexAction()
    {
        $this->dispatch(['message' => 'Service ready']);
    }
}
```

## Job Queue System

The IamLab project includes a comprehensive job queue system for handling background tasks, scheduled jobs, and asynchronous processing. The system provides reliable job processing with retry logic, priority handling, and comprehensive management tools.

### Architecture Overview

The job queue system consists of several key components:

#### Core Components

1. **JobQueue Service** (`IamLab/Service/JobQueue.php`)
   - Core service for job creation, scheduling, and management
   - Handles job dispatch with different priority levels
   - Provides job processing and failure handling
   - Supports delayed and scheduled job execution

2. **Job Model** (`IamLab/Model/Job.php`)
   - Database model representing queued jobs
   - Stores job type, payload, status, priority, and metadata
   - Includes methods for job state management

3. **JobsApi Controller** (`IamLab/Service/JobsApi.php`)
   - REST API endpoints for job management
   - Extends `aAPI` base class for standardized functionality
   - Provides CRUD operations and bulk actions

4. **WorkerCommand** (`IamLab/Core/Command/WorkerCommand.php`)
   - Command-line worker for processing jobs
   - Supports continuous processing and single-job execution
   - Includes signal handling for graceful shutdown

#### Database Schema

The `jobs` table includes the following fields:
- `id` - Primary key
- `type` - Job class/type name
- `payload` - JSON-encoded job data
- `status` - Job status (pending, processing, completed, failed)
- `priority` - Job priority (1-15, higher = more priority)
- `attempts` - Current attempt count
- `max_attempts` - Maximum retry attempts (default: 3)
- `error_message` - Error details for failed jobs
- `scheduled_at` - When job should be executed
- `started_at` - When job processing began
- `completed_at` - When job was completed
- `created_at` - Job creation timestamp
- `updated_at` - Last update timestamp

### Creating and Dispatching Jobs

#### Basic Job Dispatch

```php
use IamLab\Service\JobQueue;

$jobQueue = new JobQueue();

// Dispatch a basic job
$job = $jobQueue->dispatch('SendEmailJob', [
    'to' => 'user@example.com',
    'subject' => 'Welcome!',
    'message' => 'Welcome to our platform!'
]);
```

#### Priority Levels

```php
// Normal priority (default)
$job = $jobQueue->dispatch('ProcessDataJob', $payload, Job::PRIORITY_NORMAL);

// High priority
$job = $jobQueue->dispatchHigh('SendEmailJob', $payload);

// Critical priority
$job = $jobQueue->dispatchCritical('SystemAlertJob', $payload);
```

#### Scheduled Jobs

```php
// Schedule job for specific time
$job = $jobQueue->schedule('SendReminderJob', $payload, '2025-08-24 10:00:00');

// Delay job by seconds
$job = $jobQueue->delay('CleanupJob', $payload, 3600); // 1 hour delay
```

#### Advanced Options

```php
$job = $jobQueue->dispatch(
    'ComplexJob',
    $payload,
    Job::PRIORITY_HIGH,
    '2025-08-24 15:30:00', // scheduled time
    5 // max attempts
);
```

### Job Handlers

Job handlers are classes that implement the actual job logic. They must have a `handle()` method:

```php
<?php
namespace IamLab\Jobs;

class SendEmailJob
{
    public function handle(array $payload): bool
    {
        $to = $payload['to'];
        $subject = $payload['subject'];
        $message = $payload['message'];
        
        // Send email logic here
        $success = mail($to, $subject, $message);
        
        return $success;
    }
}
```

#### Job Handler Location

The system looks for job handlers in two locations:
1. **Exact class name**: If the job type is a fully qualified class name
2. **Jobs namespace**: `IamLab\Jobs\{JobType}`

### Running Queue Workers

#### Using Phalcon Commands

The recommended way to run queue workers is through Phalcon commands:

```bash
# Run worker continuously
./phalcons phalcon queue:work

# Run with debug mode
./phalcons debug queue:work
```

#### Worker Command Options

If using the WorkerCommand directly:

```bash
# Process jobs continuously
./phalcons command worker:run

# Process one job and exit
./phalcons command worker:run --once

# Process maximum 10 jobs
./phalcons command worker:run --jobs=10

# Run for maximum 30 minutes
./phalcons command worker:run --timeout=1800

# Sleep 5 seconds when no jobs available
./phalcons command worker:run --sleep=5

# Set maximum memory usage (256MB)
./phalcons command worker:run --max-memory=256
```

**Note**: The WorkerCommand needs to be registered in `IamLab/config/commands.php`:

```php
'worker:run' => [
    'class' => 'IamLab\\Core\\Command\\WorkerCommand',
    'description' => 'Run the job queue worker'
],
```

### API Endpoints

The JobsApi provides comprehensive REST endpoints for job management:

#### List Jobs
```http
GET /api/jobs?status=pending&limit=10&offset=0
```

#### Get Specific Job
```http
GET /api/jobs/{id}
```

#### Create Job
```http
POST /api/jobs
Content-Type: application/json

{
    "type": "SendEmailJob",
    "payload": {
        "to": "user@example.com",
        "subject": "Hello",
        "message": "Hello from the job queue!"
    },
    "priority": 5,
    "scheduled_at": "2025-08-24 10:00:00",
    "max_attempts": 3
}
```

#### Cancel Job
```http
DELETE /api/jobs/{id}
```

#### Retry Failed Job
```http
POST /api/jobs/{id}/retry
```

#### Get Queue Statistics
```http
GET /api/jobs/stats
```

Response:
```json
{
    "pending": 15,
    "processing": 2,
    "completed": 1250,
    "failed": 8,
    "total": 1275
}
```

#### Cleanup Old Jobs
```http
POST /api/jobs/cleanup
Content-Type: application/json

{
    "days": 30
}
```

#### Bulk Operations
```http
POST /api/jobs/bulk
Content-Type: application/json

{
    "action": "cancel",
    "job_ids": [1, 2, 3, 4, 5]
}
```

### Job Processing Workflow

1. **Job Creation**: Jobs are created and stored in the database with `pending` status
2. **Worker Polling**: Workers continuously poll for available jobs
3. **Job Selection**: Workers select jobs based on priority and schedule
4. **Processing**: Job status changes to `processing` and handler is executed
5. **Completion**: Successful jobs are marked as `completed`
6. **Failure Handling**: Failed jobs are retried up to `max_attempts` or marked as `failed`

### Job States

- **pending**: Job is waiting to be processed
- **processing**: Job is currently being executed
- **completed**: Job finished successfully
- **failed**: Job failed after all retry attempts
- **retrying**: Job failed but will be retried

### Best Practices

#### Job Design
- Keep jobs small and focused on a single task
- Make jobs idempotent (safe to run multiple times)
- Include all necessary data in the job payload
- Handle exceptions gracefully in job handlers

#### Performance
- Use appropriate priority levels for different job types
- Monitor queue depth and processing times
- Scale workers based on job volume
- Clean up old completed jobs regularly

#### Error Handling
- Log detailed error information for debugging
- Set appropriate `max_attempts` for different job types
- Implement exponential backoff for retries
- Monitor failed jobs and investigate patterns

#### Monitoring
```php
// Get queue statistics
$stats = $jobQueue->getStats();

// Get recent jobs
$recentJobs = $jobQueue->getRecentJobs(50);

// Get jobs by status
$failedJobs = $jobQueue->getJobsByStatus('failed', 10, 0);
```

### Example Usage Scenarios

#### Email Notifications
```php
$jobQueue->dispatch('SendWelcomeEmailJob', [
    'user_id' => $user->getId(),
    'email' => $user->getEmail(),
    'name' => $user->getName()
]);
```

#### Data Processing
```php
$jobQueue->dispatchHigh('ProcessCsvImportJob', [
    'file_path' => '/uploads/data.csv',
    'user_id' => $currentUser->getId(),
    'import_type' => 'users'
]);
```

#### Scheduled Maintenance
```php
$jobQueue->schedule('DatabaseCleanupJob', [
    'tables' => ['logs', 'sessions', 'temp_files'],
    'older_than_days' => 30
], '2025-08-24 02:00:00'); // Run at 2 AM
```

#### System Monitoring
```php
$jobQueue->delay('HealthCheckJob', [
    'services' => ['database', 'redis', 'external_api'],
    'notify_on_failure' => true
], 300); // Check in 5 minutes
```

### Troubleshooting

#### Common Issues

1. **Jobs not processing**
   - Check if workers are running
   - Verify job handlers exist and are properly named
   - Check database connectivity
   - Review error logs

2. **High failure rates**
   - Examine job handler logic for bugs
   - Check external service availability
   - Review job payload data
   - Consider increasing `max_attempts`

3. **Performance issues**
   - Monitor worker memory usage
   - Check database query performance
   - Consider job batching for bulk operations
   - Scale worker processes

#### Debugging Commands

```bash
# List available commands
./phalcons command list

# Check job queue statistics
curl -X GET http://localhost:8080/api/jobs/stats

# View recent failed jobs
curl -X GET "http://localhost:8080/api/jobs?status=failed&limit=10"

# Test job creation
curl -X POST http://localhost:8080/api/jobs \
  -H "Content-Type: application/json" \
  -d '{"type":"TestJob","payload":{"test":true}}'
```

---

*Last updated: 2025-08-23*