# Phalcon Stub Project - Changelog

## 2026-01-20 — Phalcon Upgrade

### Summary
- Upgraded Phalcon PHP extension to `v5.10.0` to resolve a critical bug in the Micro component.
- Aligned Dockerfiles to use the `PHALCON_VERSION` build argument.

### Backend (Docker)
- Updated `docker/8.4/Dockerfile` and `docker/8.5/Dockerfile` to use `PHALCON_VERSION` (defaulting to `5.10.0`).
- Ensured `docker-compose.yml` defaults to `PHALCON_VERSION=5.10.0`.

---

## 2026-01-03 — Adjustments: Keep PHP 8.4, update DB/cache, fix NPM audit

Summary
- Keep runtime on PHP 8.4 (8.5 caused compatibility concerns for this project)
- Upgrade database/cache containers to newest stable images
- Resolve NPM audit Bootstrap 3 vulnerability; fix pusher-js version resolution

Backend (Docker)
- .env: ensure `PHP_VERSION=8.4`
- docker-compose:
  - MySQL image switched to `mysql:8.4` (official) with `mysqladmin ping` healthcheck
  - Redis image pinned to `redis:7.4-alpine`

Frontend (NPM)
- Removed `bootstrap-sweetalert` (pulled vulnerable `bootstrap@~3`); project uses Tailwind + DaisyUI and central toast handler instead
- Set `pusher-js` to `^8.4.0` (8.5.0 not published at this time)

Security note (webpack-dev-server advisory)
- `laravel-mix` depends on `webpack-dev-server`, which currently has a moderate advisory without a patch. Mitigations:
  - Prefer `npm run watch` over `npm run hot` during local dev
  - Do not expose dev server publicly; keep it on localhost
  - Use Chromium-based browsers when testing HMR
  - Consider migrating to Vite in the future

How to apply
1) Rebuild containers and start:
   - `./phalcons build --no-cache`
   - `./phalcons up -d`
2) Reinstall JS deps and rebuild assets:
   - `./phalcons npm install`
   - `./phalcons npm run dev`
3) Run tests:
   - `./phalcons test`

---

## 2026-01-03 — Platform Upgrades (PHP, Docker, Composer, NPM)

### Summary
- Upgraded default PHP runtime to 8.5 and refreshed Docker images and tooling
- Modernized Composer dependencies to stable versions (no more dev-main/dev-master where avoidable)
- Bumped Node toolchain to Node.js 22 LTS inside Docker
- Updated front-end packages (Tailwind 3.4, DaisyUI 5.x, Autoprefixer, Sass, etc.) to current stable

### Backend (Docker / PHP)
- .env: set `PHP_VERSION=8.5`
- docker-compose: default `PHALCON_VERSION` updated to `5.9.2`
- docker/8.5/Dockerfile:
  - Node LTS `NODE_VERSION=22`
  - Install Composer latest
  - Ensure PHP extensions include both `memcache` and `memcached` (compat with existing services)
  - Hardened `start-container` script (UID remap safe fallback)
- docker/8.4/Dockerfile: aligned to Node 22 and ensured `memcache` is present

### PHP Dependencies (composer.json)
- Added `"prefer-stable": true`
- Upgrades (selected):
  - `symfony/var-dumper` `^6.4`
  - `symfony/finder` `^6.4`
  - `defuse/php-encryption` `^2.4`
  - `nesbot/carbon` `^3.7`
  - `league/flysystem` `^3.0`
  - `pusher/pusher-php-server` `^7.4`
  - `firebase/php-jwt` `^6.10`
  - `phpunit/phpunit` `^10.5` (kept in require to preserve current layout)
- Kept `ext-memcache` due to usage in `IamLab/config/services.php`

### Frontend (package.json)
- Tailwind CSS `^3.4.15`
- DaisyUI `^5.1.6`
- Autoprefixer `^10.4.20`
- Sass `^1.79.4`
- jQuery `^3.7.1`
- Filepond `^4.31.1`
- Pusher JS `^8.5.0`
- Font Awesome Free `^6.6.0`

### How to Rebuild Locally
1. Rebuild containers:
   - `./phalcons build --no-cache`
   - `./phalcons up -d`
2. Update PHP deps (inside Docker via helper):
   - `./phalcons composer update --no-interaction --with-all-dependencies`
3. Update JS deps and rebuild assets:
   - `./phalcons npm install`
   - `./phalcons npm run dev` (or `prod`)
4. Run tests:
   - `./phalcons test`

### Notes
- DaisyUI 5 works with Tailwind 3.4, Laravel Mix 6 remains supported
- If you rely on the `Memcache` class, it remains supported; both `memcache` and `memcached` extensions are installed in images
- If you encounter dependency conflicts, clear caches and rebuild with no cache, then run Composer with `--with-all-dependencies`

---

## Latest Updates - Documentation & Features Enhancement

### 🎯 What Was Accomplished

#### 1. Enhanced Welcome Page
- **Updated Feature Showcase**: Expanded from 2 to 6 feature cards
- **New Features Highlighted**:
  - 🔐 Authentication (enhanced description)
  - 📧 Email Service (new)
  - ⚡ Real-time Communication (new)
  - 📁 File Upload (new)
  - ⚙️ Settings (existing)
  - 🛠️ Developer Tools (new)
- **Improved Layout**: Changed to responsive 3-column grid on large screens
- **Better UX**: Added action buttons for testable features

#### 2. Created Pusher Test Page
- **Interactive Testing Interface**: New `/pusher-test` route
- **Real-time Features**:
  - Connection status monitoring
  - Live message sending/receiving
  - Multi-tab testing capability
  - Message history with timestamps
  - Error handling and logging
- **User-friendly Design**: Clean DaisyUI interface with instructions
- **Developer Tools**: Console logging and debugging features

#### 3. Updated Navigation
- **Added Pusher Test Link**: Easy access to real-time testing
- **Improved Layout**: Better button styling and spacing
- **Maintained Simplicity**: Clean, minimal navigation structure

#### 4. Comprehensive Documentation Updates

##### README.md Enhancements
- **New Sections Added**:
  - ⚡ Real-time Communication details
  - 📧 Email System documentation
  - Testing Features guide
  - Extending the Stub guidelines
  - Feature Roadmap reference
- **Updated API Endpoints**: Complete list including Pusher and auth endpoints
- **Enhanced Database Schema**: Added password reset tokens table
- **Implementation Guidelines**: Clear patterns for adding new features

##### Created FEATURES_TODO.md
- **Comprehensive Feature List**: 285 lines of detailed feature planning
- **Priority-based Organization**:
  - ✅ Implemented Features (current state)
  - 🚀 High Priority (next to implement)
  - 🔧 Medium Priority (future enhancements)
  - 📋 Low Priority (nice-to-have)
- **Detailed Categories**:
  - Authentication & Authorization (RBAC, 2FA, OAuth)
  - Content Management (CMS, Media Library)
  - Communication Features (Notifications, Chat)
  - API & Integration (REST API, Webhooks)
  - Performance & Monitoring (Caching, Logging)
  - Security Features (Advanced security, Audit trails)
  - And much more...
- **Implementation Guidelines**: Step-by-step process for adding features
- **Code Quality Standards**: Best practices and performance considerations
- **Resource Links**: Documentation and learning resources

#### 5. Technical Improvements
- **Bootstrap Integration**: Added PusherClient to global scope
- **Route Management**: Clean routing structure for new features
- **Component Architecture**: Modular, reusable components
- **Error Handling**: Comprehensive error management in Pusher test

### 🚀 Features Now Available

#### Fully Implemented & Tested
1. **Complete Authentication System**
   - User registration with validation
   - Login/logout functionality
   - Password reset with email tokens
   - Session management

2. **Email Service**
   - Multiple providers (MailHog, Resend)
   - Helper functions for easy sending
   - HTML and plain text support
   - Development testing with MailHog

3. **Real-time Communication**
   - Pusher.js integration
   - WebSocket connections
   - Channel subscriptions
   - Event triggering
   - Interactive test interface

4. **File Upload System**
   - FilePond integration
   - Drag & drop support
   - File metadata management
   - Temporary storage handling

5. **Site Settings Management**
   - Flexible key-value system
   - Multiple data types support
   - SEO and meta tag management
   - Theme configuration

6. **Modern Frontend Stack**
   - Tailwind CSS + DaisyUI
   - Mithril.js framework
   - Responsive design
   - Component-based architecture

7. **Developer Experience**
   - Docker development environment
   - Database migrations
   - Seeders for initial data
   - Modern build tools (Laravel Mix)

### 🎯 Ready for Production Use

The stub project now includes:
- **Complete user authentication flow**
- **Real-time communication capabilities**
- **Email functionality for notifications**
- **File upload handling**
- **Comprehensive documentation**
- **Clear roadmap for future development**

### 🔄 Next Steps

Developers using this stub can:
1. **Start Building Immediately**: All core features are ready
2. **Follow the Roadmap**: Use FEATURES_TODO.md for feature planning
3. **Test Real-time Features**: Use `/pusher-test` for WebSocket testing
4. **Customize as Needed**: Follow established patterns for new features
5. **Scale Confidently**: Built with production-ready architecture

### 📚 Documentation Structure

```
├── README.md              # Main project documentation
├── FEATURES_TODO.md        # Comprehensive feature roadmap
├── CHANGELOG.md           # This file - update history
└── .env.example           # Environment configuration template
```

### 🛠️ For Developers

When extending this stub:
1. **Check FEATURES_TODO.md** for planned features
2. **Follow the implementation guidelines** in the documentation
3. **Use the established patterns** for consistency
4. **Test with the provided tools** (Pusher test, MailHog)
5. **Update documentation** as you add features

---

**This stub project is now a comprehensive foundation for building modern web applications with Phalcon PHP framework, complete with real-time features, email capabilities, and extensive documentation for future development.**