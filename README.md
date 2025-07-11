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

### ⚡ Real-time Communication
- **Pusher Service** (`IamLab\Core\Pusher\PusherService`): WebSocket real-time communication
- **Pusher API** (`IamLab\Service\PusherApi`): Backend API for Pusher integration
- **Frontend Client** (`assets/js/components/PusherClient.js`): JavaScript client for real-time features
- **Test Page**: Interactive Pusher testing interface at `/pusher-test`

### 📧 Email System
- **Email Service** (`IamLab\Core\Email\EmailService`): Configurable email service
- **Multiple Providers**: MailHog (development) and Resend (production) support
- **Email Helper** (`email()` function): Simple email sending utility
- **Password Reset**: Complete forgot password workflow with email tokens

### 🏗️ Core Framework Components
- **API Framework** (`IamLab\Core\API`): RESTful API foundation
- **Collections** (`IamLab\Core\Collection`): Data collection utilities
- **Enumerations** (`IamLab\Core\Enum`): Type-safe enumerations
- **Environment Handling** (`IamLab\Core\Env`): Configuration management
- **Helpers** (`IamLab\Core\Helpers`): Common utility functions

## Technology Stack

- **Backend**:
  - PHP 8.0+
  - Phalcon Framework 5.x
  - PostgreSQL
  - RESTful API Architecture

- **Frontend Ready**:
  - Asset pipeline configured
  - SASS/SCSS support
  - Modern JavaScript build tools
  - FilePond integration ready

## Quick Start

### Prerequisites

- Docker & Docker Compose
- PHP 8.0+
- Composer
- Node.js & NPM

### Installation

1. **Clone or download this stub project**:
   ```bash
   git clone <your-stub-repo-url> your-new-project
   cd your-new-project
   ```

2. **Start Docker containers**:
   ```bash
   ./phalcons up -d
   ```

3. **Install dependencies**:
   ```bash
   ./phalcons composer install
   ./phalcons npm install
   ```

4. **Run database migrations**:
   ```bash
   ./phalcons migrate
   ```

5. **Seed the database with initial data**:
   ```bash
   ./phalcons migrate:seed --email="admin@example.com" --password="YourSecurePassword123!"
   ```

6. **Build frontend assets**:
   ```bash
   ./phalcons npm run dev
   ```

### Development

- **Start development server**: `./phalcons up`
- **Run migrations**: `./phalcons migrate`
- **Access application**: `http://localhost:8080`
- **Database**: PostgreSQL on `localhost:5432`

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

## Testing Features

### Real-time Communication Test
Visit `/pusher-test` to test the Pusher integration:
- Check connection status
- Send test messages
- See real-time communication in action
- Open multiple browser tabs to test multi-user functionality

### Email Testing
The project includes MailHog for email testing in development:
- Access MailHog dashboard at `http://localhost:8025`
- Test password reset emails
- View all sent emails during development

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

## Generating New Projects from This Stub

This Phalcon Stub project is designed as a Composer package template for generating new PHfalcon and Miral projects. You can use it to quickly bootstrap new applications with all the essential components already configured.

### Quick Start - Generate a New Project

```bash
# Generate a new project using Composer
composer create-project iam-lab/phalcon-stub your-new-project

# Navigate to your new project
cd your-new-project

# Start development environment
./phalcons up -d

# Run migrations and build assets
./phalcons migrate
./phalcons npm run dev

# Visit your new application
open http://localhost:8080
```

### Comprehensive Guide

For detailed instructions on project generation, customization, and deployment, see:

**📖 [PROJECT_GENERATION.md](_docs/PROJECT_GENERATION.md)** - Complete guide for generating and customizing new projects

This guide covers:
- Two methods for project generation (Composer create-project and manual clone)
- Environment configuration and customization
- Namespace changes and branding updates
- Development workflow and deployment
- Troubleshooting common issues

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
