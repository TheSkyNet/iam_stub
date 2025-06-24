# Generating New Projects from Phalcon Stub

This guide explains how to use the Phalcon Stub project as a template to generate new PHfalcon and Miral projects using Composer.

## Overview

The Phalcon Stub project is designed as a Composer package template that provides a clean, minimal foundation for building modern web applications with the Phalcon PHP framework. It includes essential components like user authentication, email services, real-time communication, and file handling.

## Prerequisites

Before generating a new project, ensure you have:

- **PHP 8.0+** installed
- **Composer** installed globally
- **Docker & Docker Compose** (for development environment)
- **Node.js & NPM** (for frontend assets)
- **Git** (for version control)

## Method 1: Using Composer Create-Project (Recommended)

### Step 1: Generate New Project

Use Composer's `create-project` command to generate a new project from this stub:

```bash
composer create-project iam-lab/phalcon-stub your-new-project
```

Replace `your-new-project` with your desired project name.

### Step 2: Navigate to Project Directory

```bash
cd your-new-project
```

### Step 3: Configure Environment

The `.env` file will be automatically created from `.env.example`. Update it with your project-specific configuration:

```bash
# Edit the environment file
nano .env

# Update these key values:
APP_NAME="Your Project Name"
MAIL_FROM_EMAIL=noreply@yourproject.com
MAIL_FROM_NAME="Your Project Name"

# Add your Pusher credentials if using real-time features
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster
```

### Step 4: Start Development Environment

```bash
# Start Docker containers
./phalcons up -d

# Install PHP dependencies (if not already done)
./phalcons composer install

# Install Node.js dependencies
./phalcons npm install

# Run database migrations
./phalcons migrate

# Build frontend assets
./phalcons npm run dev
```

### Step 5: Access Your Application

- **Main Application**: http://localhost:8080
- **MailHog Dashboard**: http://localhost:8025 (for email testing)
- **Database**: PostgreSQL on localhost:5432

## Method 2: Manual Clone and Setup

If you prefer to clone the repository manually:

### Step 1: Clone Repository

```bash
git clone https://github.com/iam-lab/phalcon-stub.git your-new-project
cd your-new-project
```

### Step 2: Remove Git History and Reinitialize

```bash
# Remove existing git history
rm -rf .git

# Initialize new git repository
git init
git add .
git commit -m "Initial commit from Phalcon Stub"
```

### Step 3: Update Project Configuration

```bash
# Copy environment file
cp .env.example .env

# Update composer.json with your project details
nano composer.json
```

Update the following in `composer.json`:
- `name`: Change to your project name (e.g., "your-company/your-project")
- `description`: Update with your project description
- `homepage`: Update with your project URL
- `authors`: Update with your information

### Step 4: Follow Steps 3-5 from Method 1

Continue with environment configuration and setup as described in Method 1.

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

For **development** (using MailHog):
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

Test the configuration at: http://localhost:8080/pusher-test

### 5. Customize Namespace (Optional)

If you want to change the namespace from `IamLab` to your own:

1. **Update composer.json autoload section**:
```json
{
    "autoload": {
        "psr-4": {
            "YourNamespace\\": "YourNamespace/"
        }
    }
}
```

2. **Rename directory**: `mv IamLab YourNamespace`

3. **Update all PHP files** to use the new namespace

4. **Run composer dump-autoload**: `./phalcons composer dump-autoload`

## Available Features

Your new project includes:

### ✅ Core Features
- **User Authentication**: Registration, login, logout, password reset
- **Email Service**: Multi-provider support (MailHog, Resend)
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
- **PostgreSQL**: Database
- **MailHog**: Email testing
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
# Visit: http://localhost:8080/pusher-test

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
- Ensure PostgreSQL container is running: `./phalcons ps`

3. **Asset Compilation Issues**:
```bash
./phalcons npm install
./phalcons npm run dev
```

4. **Email Not Working**:
- For development: Check MailHog at http://localhost:8025
- For production: Verify email provider credentials

### Getting Help

- **Documentation**: Check `README.md` and individual component documentation in `IamLab/Core/*/README.md`
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
composer create-project iam-lab/phalcon-stub my-awesome-app

# Navigate to project
cd my-awesome-app

# Update environment
cp .env.example .env
# Edit .env with your configuration

# Start development environment
./phalcons up -d

# Install dependencies
./phalcons composer install
./phalcons npm install

# Set up database
./phalcons migrate

# Build assets
./phalcons npm run dev

# Test the application
open http://localhost:8080

# Test email functionality
./phalcons command test:mail your@email.com

# Test real-time features
open http://localhost:8080/pusher-test
```

Your new Phalcon project is now ready for development! 🚀

---

**Happy coding with your new Phalcon project!**
