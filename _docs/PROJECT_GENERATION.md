# Generating New Projects from Phalcon Stub

This guide explains how to use the Phalcon Stub project as a template to generate new PHfalcon and Miral projects using Composer.

## Overview

The Phalcon Stub project is designed as a Composer package template that provides a clean, minimal foundation for building modern web applications with the Phalcon PHP framework. It includes essential components like user authentication, email services, real-time communication, and file handling.

## Prerequisites

Before generating a new project, ensure you have:

- **Docker & Docker Compose** (for development environment)
- **Git** (for version control)

The application image supplies PHP 8.5.12, Phalcon 5.23.0, Composer 2.10, Node.js 24, and npm 11.

## Create the project

After the first stable release is imported by Packagist, generate a new project with:

```bash
composer create-project iam-lab/phalcon-stub <directory> --no-install
```

*Note: If your host environment does not provide PHP 8.5 and Phalcon, append `--ignore-platform-reqs`.*

Until a stable release is available, select the development branch explicitly:

```bash
composer create-project iam-lab/phalcon-stub:dev-main <directory> --no-install --ignore-platform-reqs
```

`--no-install` defers dependency installation to the application container.
Composer still checks the template's platform requirements while selecting it, so
use `--ignore-platform-reqs` when the host does not provide PHP 8.5 and Phalcon.
The application container provides those requirements.

## Set up the generated project

```bash
cd <directory>
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

The environment command preserves an existing `.env`. Edit that file to set your
application name, email settings, database credentials, and optional Pusher credentials.

### Access your application

- **Main Application**: http://localhost:8085
- **Mailpit Dashboard**: http://localhost:8025 (for email testing)
- **Database**: MySQL 8.4 on localhost:3306

## Post-Generation Customization

### 1. Update Branding and Content

- **Update Welcome Page**: Edit `assets/js/components/Welcome.js`
- **Update Navigation**: Edit `assets/js/components/layout.js`
- **Update Admin Layout**: Edit `assets/js/Admin/adminLayout.js`
- **Update README**: Replace this README with your project-specific documentation

### 2. Configure Database

```bash
# Update database credentials in .env
DB_HOST=your_db_host
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
DB_NAME=your_db_name

# Run migrations
./phalcons migrate
```

### 3. Configure Email Service

For **development** (using Mailpit with compatibility environment names):
```env
MAIL_PROVIDER=mailhog
MAILHOG_HOST=mailhog
MAILHOG_PORT=1025
```

For **production** (using Resend):
```env
MAIL_PROVIDER=resend
RESEND_API_KEY=your_resend_api_key
```

### 4. Configure Real-time Features

If using Pusher for real-time features:
```env
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster
```

Test the configuration at: http://localhost:8085/pusher-test

### 5. Customize Namespace (Optional)

After installing dependencies, use the project initializer to rename the package,
the `IamLab` namespace, and its directory. Then regenerate Composer autoloading:

```bash
./phalcons project:init my-new-project MyNewProject --force
./phalcons composer dump-autoload
```

## Available Features

Your new project includes:

### ✅ Core Features
- **User Authentication**: Registration, login, logout, password reset
- **Email Service**: Multi-provider support (Mailpit-compatible SMTP, Resend)
- **Real-time Communication**: Pusher.js integration
- **File Upload**: FilePond integration
- **Site Settings**: Flexible configuration system
- **Command System**: Custom CLI commands

### 🎨 Frontend Stack
- **Tailwind CSS + DaisyUI**: Modern utility-first CSS framework
- **Mithril.js**: Lightweight frontend framework
- **Laravel Mix**: Asset compilation and optimization

### 🐳 Development Environment
- **Docker Compose**: Complete development environment
- **MySQL 8.4 LTS**: Database
- **Mailpit**: Email testing
- **Hot Reloading**: Asset watching and compilation

## Development Workflow

### Daily Development

```bash
# Start development environment
./phalcons up -d

# Watch and compile assets
./phalcons npm run watch

# Run migrations (when needed)
./phalcons migrate

# Access application logs
./phalcons logs

# Access container shell
./phalcons shell
```

### Adding New Features

1. **Check the Feature Roadmap**: See `FEATURES_TODO.md` for planned features
2. **Follow Established Patterns**: Use existing code structure as a guide
3. **Update Documentation**: Keep README and documentation current
4. **Add Tests**: Include tests for new functionality

### Testing

```bash
# Test email functionality
./phalcons command test:mail your@email.com -v

# Test real-time features
# Visit: http://localhost:8085/pusher-test

# Run custom commands
./phalcons command list
```

## Deployment

### Production Setup

1. **Update Environment**: Set `APP_ENV=production` and `APP_DEBUG=false`
2. **Configure Production Database**: Update database credentials
3. **Configure Production Email**: Use Resend or other production email service
4. **Build Assets**: Run `./phalcons npm run production`
5. **Set Up SSL**: Configure HTTPS for your domain
6. **Configure Pusher**: Set up production Pusher credentials

### Docker Production

The project includes Docker configuration suitable for production deployment. Update the docker-compose.yml file for your production environment.

## Troubleshooting

### Common Issues

1. **Permission Issues**:
```bash
sudo chown -R $USER:$USER .
chmod +x phalcons
```

2. **Database Connection Issues**:
- Check database credentials in `.env`
- Ensure the MySQL container is running: `./phalcons ps`

3. **Asset Compilation Issues**:
```bash
./phalcons npm ci
./phalcons npm run dev
```

4. **Email Not Working**:
- For development: Check Mailpit at http://localhost:8025
- For production: Verify email provider credentials

### Getting Help

- **API Framework Documentation**: See `_docs/ROUTE_GROUPS_DOCUMENTATION.md`
- **Frontend Services Approach**: See `_docs/FRONTEND_SERVICES.md` for the recommended pattern for using services in Mithril components
- **Error Handling**: See `_docs/ERROR_SERVICE.md` for information on global error handling and toasts
- **Feature Requests**: See `FEATURES_TODO.md` for planned features
- **Issues**: Check the project repository for known issues and solutions

## Next Steps

After generating your project:

1. **Customize the Welcome Page**: Update branding and content
2. **Add Your Features**: Use the feature roadmap as a guide
3. **Set Up Version Control**: Initialize git and set up your repository
4. **Configure CI/CD**: Set up automated testing and deployment
5. **Add Your Team**: Update author information and add contributors

## Example: Complete Project Generation

Here's a complete example of generating a new project called "MyAwesomeApp":

```bash
# Generate new project
composer create-project iam-lab/phalcon-stub:dev-main my-awesome-app --no-install --ignore-platform-reqs

# Navigate to project
cd my-awesome-app

# Preserve an existing environment file, or create it from the example.
[ -f .env ] || cp .env.example .env

# Start development environment
./phalcons up -d

# Install dependencies
./phalcons composer install
./phalcons npm ci
./phalcons npm run browser:install

# Optionally customize the project and namespace.
./phalcons project:init my-awesome-app MyAwesomeApp --force
# Required only when project:init was run.
./phalcons composer dump-autoload

# Set up database
./phalcons migrate

# Optionally create the first administrator account.
./phalcons migrate:seed --email="admin@example.com" --password="YourSecurePassword123!"

# Build assets
./phalcons npm run dev

# Test the application
open http://localhost:8085

# Test email functionality
./phalcons command test:mail your@email.com

# Test real-time features
open http://localhost:8085/pusher-test
```

Your new Phalcon project is now ready for development! 🚀

---

**Happy coding with your new Phalcon project!**
