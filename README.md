# Phalcon Project Stub

A clean, minimal Phalcon PHP framework stub project designed to serve as a foundation for generating new Phalcon and Mithril.js projects. This stub includes essential components for user authentication, site settings management, and file handling.

## Purpose

This project serves as a template/stub for creating new Phalcon-based applications. It provides:

- **Clean Foundation**: Minimal, well-structured codebase without application-specific logic
- **Essential Components**: Core functionality that most web applications need
- **Easy Customization**: Ready to be extended for specific project requirements
- **Best Practices**: Follows Phalcon framework conventions and PHP best practices

## Included Components

### 🔐 User Authentication System
- **User Model** (`IamLab\Model\User`): Complete user management with email/password authentication
- **Auth Service** (`IamLab\Service\Auth`): Authentication, login, logout, and session management
- **Auth API** (`IamLab\Service\Auth\AuthService`): RESTful authentication endpoints

### ⚙️ Site Settings Management
- **SiteSetting Model** (`IamLab\Model\SiteSetting`): Flexible key-value configuration system
- **Settings Service** (`IamLab\Service\SettingsService`): Comprehensive settings management with support for:
  - Meta tags and SEO settings
  - Social media integration (Open Graph, Twitter)
  - Theme configuration
  - Analytics configuration
  - Maintenance mode
  - Structured data for schema.org

### 📁 File Upload System (FilePond)
- **Filepond Model** (`IamLab\Model\Filepond`): File metadata and temporary storage management
- **Filepond Services**: Complete file upload handling with FilePond integration
  - `FilepondApi.php`: API endpoints for file operations
  - `FilepondFile.php`: File handling utilities
  - `FilepondService.php`: Core file processing logic

### ⚡ Real-time Communication & SSE
- **Pusher Service** (`IamLab\Core\Pusher\PusherService`): WebSocket real-time communication
- **Pusher API** (`IamLab\Service\PusherApi`): Backend API for Pusher integration
- **Frontend Client** (`assets/js/components/PusherClient.js`): JavaScript client for real-time features
- **Server-Sent Events (SSE)**: Efficient one-way streaming from server to client
  - `IamLab\Service\SseApi`: API endpoints for SSE streams
  - `IamLab\Core\SSE`: Core SSE implementation
- **Test Pages**: Interactive testing interfaces at `/demo`

### 🔄 Job Queue & Worker System
- **Job Model** (`IamLab\Model\Job`): Database-backed job queue with status tracking
- **JobQueue Service** (`IamLab\Service\JobQueue`): Job creation, scheduling, and management
- **Jobs API** (`IamLab\Service\JobsApi`): RESTful API for job management
- **Worker Command** (`IamLab\Core\Command\WorkerCommand`): CLI worker for processing jobs
- **Example Jobs**: SendEmailJob, ProcessDataJob, CleanupJob for demonstration

### 📧 Email System
- **Email Service** (`IamLab\Core\Email\EmailService`): Configurable email service
- **Multiple Providers**: Mailpit-compatible SMTP (development) and Resend (production) support
- **Email Helper** (`email()` function): Simple email sending utility
- **Password Reset**: Complete forgot password workflow with email tokens

### 🎓 LMS Integration System
- **LMS Service** (`IamLab\Service\LMS\LMSService`): Core service for LMS and AI integrations
- **Multi-Provider Support**: Factory pattern for Ollama, Google Gemini, and Tencent EDU
- **Registry & Health**: Integrated health checks and fallback mechanisms
- **Setup Commands**:
  - Enable Ollama: `./phalcons command ollama enable`
  - Disable Ollama: `./phalcons command ollama disable`
  - Check Status: `./phalcons command ollama status`
  - See **[LMS Integration Setup](_docs/lms-integration-setup.md)** for more details.

### 💰 Payment & Subscription System
- **Payment Service** (`IamLab\Service\Payment\PaymentService`): Unified interface for transactions
- **Supported Providers**: Stripe, PayPal, and Square integrations
- **Subscription Management**: Complete lifecycle for recurring payments
- **Factory Pattern**: Easily extendable architecture for new providers
- See **[Payment Integrations Guide](_docs/PAYMENT_INTEGRATIONS.md)** for configuration details.

### 🏗️ Core Framework Components
- **API Framework** (`IamLab\Core\API`): RESTful API foundation
- **Collections** (`IamLab\Core\Collection`): Data collection utilities
- **Enumerations** (`IamLab\Core\Enum`): Type-safe enumerations
- **Environment Handling** (`IamLab\Core\Env`): Configuration management
- **Helpers** (`IamLab\Core\Helpers`): Common utility functions

## Technology Stack

- **Backend**:
  - PHP 8.5.12
  - Phalcon Framework 5.23.0
  - MySQL 8.4 LTS and Redis 8
  - RESTful API Architecture

- **Frontend**:
  - Node.js 24 and npm 11
  - Laravel Mix 6 with Webpack 5.105
  - Tailwind CSS 4, DaisyUI 5, and Mithril 3
  - FilePond integration ready

## Quick Start

### 🚀 Generating a New Project

After the first stable release is available on Packagist, create a project with:

```bash
composer create-project iam-lab/phalcon-stub <directory> --no-install
```

*Note: If your host environment does not provide PHP 8.5 and Phalcon, append `--ignore-platform-reqs`.*

Until then, use the currently available development branch. `--ignore-platform-reqs`
allows Composer to select the template when the host does not provide PHP 8.5 and
Phalcon; the application container provides those requirements.

```bash
composer create-project iam-lab/phalcon-stub:dev-main <directory> --no-install --ignore-platform-reqs
```

### Prerequisites

- Docker & Docker Compose
- Composer on the host, for the `composer create-project` command above.

PHP, Phalcon, Node.js, and npm are provided by the application image; no matching host runtimes are required.

### Installation

From the generated directory, run the complete Docker-first setup:

```bash
cd <directory>

# Keep an existing environment file; otherwise create it from the example.
[ -f .env ] || cp .env.example .env

./phalcons up -d
./phalcons composer install
./phalcons npm ci
./phalcons npm run browser:install

# Optional: rename the package and the default IamLab namespace.
./phalcons project:init my-new-project MyNewProject --force
# Required only when project:init was run.
./phalcons composer dump-autoload

./phalcons migrate
# Optional: create the first administrator account.
./phalcons migrate:seed --email="admin@example.com" --password="YourSecurePassword123!"
./phalcons npm run dev
```

Open `http://localhost:8085`.

For extended application customization, see
[the project-generation guide](_docs/PROJECT_GENERATION.md). Package maintainers
should use the separate [Packagist publication checklist](_docs/PACKAGIST.md).

### Development

- **Start development server**: `./phalcons up`
- **Run migrations**: `./phalcons migrate`
- **Access application**: `http://localhost:8085`
- **Database**: MySQL on `localhost:3306`

## Project Structure

```
├── IamLab/                     # Main application directory
│   ├── Core/                   # Framework core components
│   │   ├── API/               # API framework
│   │   ├── Collection/        # Data collections
│   │   ├── Enum/             # Enumerations
│   │   ├── Env/              # Environment handling
│   │   └── Helpers/          # Utility functions
│   ├── Model/                 # Data models
│   │   ├── User.php          # User authentication model
│   │   ├── SiteSetting.php   # Site configuration model
│   │   └── Filepond.php      # File upload model
│   ├── Service/              # Business logic services
│   │   ├── Auth/             # Authentication services
│   │   ├── Auth.php          # Main auth service
│   │   ├── SettingsService.php # Settings management
│   │   └── Filepond/         # File upload services
│   ├── Migrations/           # Database migrations
│   ├── config/               # Configuration files
│   ├── views/                # View templates
│   └── app.php               # Application bootstrap
├── public/                   # Web root
├── assets/                   # Frontend assets
├── docker/                   # Docker configuration
├── bin/                      # CLI scripts
└── composer.json             # PHP dependencies
```

## Customization Guide

### Adding New Models

1. Create model in `IamLab/Model/`
2. Add corresponding migration in `IamLab/Migrations/`
3. Create service in `IamLab/Service/` if needed

### Extending Authentication

The authentication system is fully functional but can be extended:
- Add user roles and permissions
- Implement OAuth providers
- Add two-factor authentication
- Customize user profile fields

### Configuring Site Settings

Add new settings by:
1. Creating entries in the `site_settings` table
2. Extending `SettingsService.php` to handle new setting types
3. Using settings in your views and controllers

### File Upload Customization

FilePond integration supports:
- Multiple file types
- File validation
- Temporary storage
- Permanent file management

## Database Schema

The stub includes migrations for:
- `users` - User authentication and profiles
- `site_settings` - Application configuration
- `filepond` - File upload metadata
- `password_reset_tokens` - Password reset functionality

## API Endpoints

### Authentication
- `POST /auth` - User login
- `POST /auth/register` - User registration
- `POST /auth/forgot-password` - Request password reset
- `POST /auth/logout` - User logout
- `GET /auth/user` - Get current user

### Real-time Communication
- `GET /api/pusher/config` - Get Pusher configuration
- `POST /api/pusher/auth` - Authenticate private/presence channels
- `POST /api/pusher/trigger` - Trigger events (testing)
- `GET /api/pusher/channel-info` - Get channel information
- `GET /api/pusher/channels` - List active channels

### File Upload
- `POST /filepond/upload` - Upload file
- `DELETE /filepond/{id}` - Delete file
- `GET /filepond/{id}` - Get file info

### Job Queue Management
- `GET /api/jobs` - List jobs with pagination and filtering
- `POST /api/jobs` - Create a new job
- `GET /api/jobs/{id}` - Get job details
- `DELETE /api/jobs/{id}` - Cancel a job
- `POST /api/jobs/{id}/retry` - Retry a failed job
- `GET /api/jobs/stats` - Get job queue statistics
- `GET /api/jobs/types` - Get available job types
- `POST /api/jobs/cleanup` - Clean up old completed jobs
- `POST /api/jobs/bulk` - Bulk operations on jobs

## 🧪 Testing & Demos

### 🚀 Demo Dashboard
Visit the **[Demo Dashboard](/demo)** to test all core integrations in one place:
- **Real-time**: Test Pusher WebSockets and SSE streams
- **Payments**: Multi-provider demo (Stripe, PayPal, Square) and a dedicated **[PayPal v6 SDK Demo](/demo/paypal)**
- **LMS**: Monitor AI service status and capabilities
- **Jobs**: Trigger and monitor background job processing
- **Auth**: Test role-based access and profile management

### 📧 Email Testing
The project includes Mailpit for email testing in development while retaining the existing `MAILHOG_*` application settings:
- Access the Mailpit dashboard at `http://localhost:8025` (check `.env` for `FORWARD_MAILHOG_DASHBOARD_PORT`)
- Test password reset emails and system notifications

### 🛠️ Setup & Integration Guides

For detailed setup instructions, refer to the following documentation:

- **[LMS Integration Setup](_docs/lms-integration-setup.md)**: Configuring AI and LMS providers
- **[Payment Integrations](_docs/PAYMENT_INTEGRATIONS.md)**: Setting up Stripe, PayPal, and Square
- **[JWT Authentication](_docs/JWT_AUTHENTICATION.md)**: Security and token configuration
- **[OAuth Setup](_docs/OAUTH_INTEGRATION.md)**: Integrating Google, GitHub, and Facebook login
- **[Role Guards](_docs/ROLE_GUARDS_DOCUMENTATION.md)**: Managing permissions and RBAC
- **[Route Groups](_docs/ROUTE_GROUPS_DOCUMENTATION.md)**: Understanding application routing
- **[Error Service](_docs/ERROR_SERVICE.md)**: Monitoring and centralized logging

### Job Queue & Worker System Testing
Test the worker system functionality:
- Create jobs via API endpoints
- Process jobs using the worker command
- Monitor job status and statistics

## Using the Job Queue & Worker System

The framework includes a comprehensive job queue and worker system for processing background tasks.

### Setup

1. **Run the migration** to create the jobs table:
   ```bash
   ./phalcons migrate
   ```

2. **Start a worker** to process jobs:
   ```bash
   php bin/console worker:run
   ```

### Creating Jobs via API

Create a new job by sending a POST request to `/api/jobs`:

```json
{
  "type": "SendEmailJob",
  "payload": {
    "to": "user@example.com",
    "subject": "Hello",
    "message": "Hello from the job queue!"
  },
  "priority": 5,
  "max_attempts": 3
}
```

### Creating Custom Job Classes

1. Create a new job class in `IamLab/Jobs/`:

```php
<?php
namespace IamLab\Jobs;

class MyCustomJob
{
    public function handle(array $payload): bool|string
    {
        // Your job logic here
        // Return true for success, string for error message
        
        return true;
    }
}
```

2. Dispatch the job:

```php
$jobQueue = new JobQueue();
$job = $jobQueue->dispatch('MyCustomJob', [
    'data' => 'your data here'
], Job::PRIORITY_HIGH);
```

### Worker Command Options

```bash
# Process jobs continuously
php bin/console worker:run

# Process one job and exit
php bin/console worker:run --once

# Process maximum 10 jobs
php bin/console worker:run --jobs=10

# Run for maximum 30 minutes
php bin/console worker:run --timeout=1800

# Sleep 5 seconds when no jobs available
php bin/console worker:run --sleep=5

# Maximum memory usage 256MB
php bin/console worker:run --max-memory=256
```

### Job Management via API

```bash
# List all jobs
GET /api/jobs

# Filter jobs by status
GET /api/jobs?status=pending

# Get job statistics
GET /api/jobs/stats

# Retry a failed job
POST /api/jobs/123/retry

# Cancel a job
DELETE /api/jobs/123

# Bulk operations
POST /api/jobs/bulk
{
  "action": "cancel",
  "job_ids": [1, 2, 3]
}
```

### Job Priorities

- `Job::PRIORITY_LOW` (1) - Low priority tasks
- `Job::PRIORITY_NORMAL` (5) - Default priority
- `Job::PRIORITY_HIGH` (10) - High priority tasks
- `Job::PRIORITY_CRITICAL` (15) - Critical tasks

### Job Statuses

- `pending` - Job is waiting to be processed
- `processing` - Job is currently being processed
- `completed` - Job completed successfully
- `failed` - Job failed after all retry attempts
- `retrying` - Job failed but will be retried

## Service Management

The framework includes comprehensive service management capabilities that allow you to run your Phalcon application as a system service. This enables automatic startup on boot, service monitoring, and easy management through standard system tools.

### Supported Platforms

- **Linux with SystemD** - Modern Linux distributions (Ubuntu 16+, CentOS 7+, etc.)
- **Linux with Init.d** - Older Linux distributions and systems without SystemD
- **macOS with LaunchD** - macOS service management

### Installing as a Service

1. **Install the service** (requires sudo privileges on Linux):
   ```bash
   ./phalcons service:install
   ```

   This command will:
   - Detect your operating system and service manager
   - Create the appropriate service file from templates
   - Configure the service with your current directory and user
   - Enable the service for automatic startup

2. **Start the service**:
   ```bash
   ./phalcons service:start
   ```

3. **Check service status**:
   ```bash
   ./phalcons service:status
   ```

### Service Management Commands

```bash
# Install the application as a system service
./phalcons service:install

# Start the application service
./phalcons service:start

# Stop the application service
./phalcons service:stop

# Restart the application service
./phalcons service:restart

# Check the application service status
./phalcons service:status

# Remove the application service
./phalcons service:uninstall
```

### Platform-Specific Details

#### Linux with SystemD

- **Service file location**: `/etc/systemd/system/phalcon-app.service`
- **Management commands**:
  ```bash
  sudo systemctl start phalcon-app
  sudo systemctl stop phalcon-app
  sudo systemctl restart phalcon-app
  sudo systemctl status phalcon-app
  sudo systemctl enable phalcon-app   # Auto-start on boot
  sudo systemctl disable phalcon-app  # Disable auto-start
  ```
- **View logs**: `sudo journalctl -u phalcon-app -f`

#### Linux with Init.d

- **Service file location**: `/etc/init.d/phalcon-app`
- **Management commands**:
  ```bash
  sudo service phalcon-app start
  sudo service phalcon-app stop
  sudo service phalcon-app restart
  sudo service phalcon-app status
  ```

#### macOS with LaunchD

- **Service file location**: `~/Library/LaunchAgents/com.phalcon.app.plist`
- **Management commands**:
  ```bash
  launchctl load ~/Library/LaunchAgents/com.phalcon.app.plist
  launchctl unload ~/Library/LaunchAgents/com.phalcon.app.plist
  launchctl list | grep com.phalcon.app
  ```
- **View logs**: Check `/var/log/phalcon-app.out.log` and `/var/log/phalcon-app.err.log`

### Service Configuration

The service is configured with the following features:

- **Automatic restart** on failure
- **Dependency management** (waits for Docker to be available)
- **Security settings** (runs with limited privileges)
- **Logging** (stdout/stderr captured)
- **Environment variables** properly set

### Troubleshooting

#### Service Won't Start

1. **Check Docker is running**:
   ```bash
   docker info
   ```

2. **Check service logs**:
   ```bash
   # SystemD
   sudo journalctl -u phalcon-app -n 50
   
   # Init.d
   tail -f /var/log/syslog | grep phalcon-app
   
   # macOS
   tail -f /var/log/phalcon-app.err.log
   ```

3. **Verify service file permissions**:
   ```bash
   # Linux
   ls -la /etc/systemd/system/phalcon-app.service
   ls -la /etc/init.d/phalcon-app
   
   # macOS
   ls -la ~/Library/LaunchAgents/com.phalcon.app.plist
   ```

#### Permission Issues

- Ensure the service user has access to the project directory
- Check Docker group membership for the service user
- Verify file permissions in the project directory

#### Port Conflicts

- Check if the configured ports are already in use
- Modify the `.env` file to use different ports if needed
- Restart the service after configuration changes

### Production Deployment

For production deployments:

1. **Create a dedicated user** for the service:
   ```bash
   sudo useradd -r -s /bin/false phalcon-app
   sudo usermod -aG docker phalcon-app
   ```

2. **Set proper ownership**:
   ```bash
   sudo chown -R phalcon-app:phalcon-app /path/to/your/app
   ```

3. **Install and configure the service**:
   ```bash
   ./phalcons service:install
   ./phalcons service:start
   ```

4. **Enable monitoring** and log rotation as needed

### Security Considerations

- The service runs with the user account that installed it
- SystemD services include security restrictions
- Log files may contain sensitive information
- Ensure proper firewall configuration for exposed ports

## Extending the Stub

### Feature Roadmap
See `FEATURES_TODO.md` for a comprehensive list of features that can be added to this stub project, including:
- Role-based access control (RBAC)
- Two-factor authentication (2FA)
- OAuth integration
- Content management system
- Advanced notifications
- Payment integration
- And much more...

### Implementation Guidelines
Each new feature should follow the established patterns:
- Database migrations in `IamLab/Migrations/`
- Models in `IamLab/Model/`
- Services in `IamLab/Service/`
- API endpoints in `IamLab/app.php`
- Frontend components in `assets/js/components/`

## Contributing

This is a stub project template. When using it:

1. **Follow the Project Generation Guide** - See [PROJECT_GENERATION.md](_docs/PROJECT_GENERATION.md)
2. **Rename the namespace** from `IamLab` to your project name
3. **Update configuration** files with your project details
4. **Customize the database** schema as needed
5. **Add your specific** business logic and models
6. **Update this README** with your project-specific information
7. **Check the features TODO list** for additional functionality to implement

## License

This stub project is provided as-is for creating new Phalcon applications. Customize and use according to your project needs.

---

**Ready to build something amazing? Start customizing this stub for your next Phalcon or Mithril.js project!** 🚀
