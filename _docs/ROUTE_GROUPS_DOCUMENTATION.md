# Route Groups Implementation

This document describes the new route grouping system implemented for organizing and protecting routes in `app.php` with shared guards and middleware.

## Overview

The RouteGroup system provides a clean way to:
- **Group related routes together** with shared prefixes
- **Apply guards to entire groups** (authentication, roles, etc.)
- **Add custom middleware** to route groups
- **Organize routes logically** instead of scattered individual definitions

## RouteGroup Class

### Location
`IamLab/Core/Routing/RouteGroup.php`

### Basic Usage

```php
use IamLab\Core\Routing\RouteGroup;

// Create a route group with optional prefix
RouteGroup::create($app, '/api')
    ->requireAuth()  // Apply authentication guard
    ->group(function ($group) {
        $group->get('/users', [UserApi::class, 'indexAction']);
        $group->post('/users', [UserApi::class, 'createAction']);
    });
```

## Available Guards

### 1. requireAuth()
Requires user authentication for all routes in the group:

```php
RouteGroup::create($app, '/api')
    ->requireAuth()
    ->group(function ($group) {
        // All routes here require authentication
        $group->get('/profile', [UserApi::class, 'profileAction']);
        $group->post('/logout', [AuthApi::class, 'logoutAction']);
    });
```

### 2. requireAdmin()
Requires authentication + admin role:

```php
RouteGroup::create($app, '/admin')
    ->requireAdmin()
    ->group(function ($group) {
        // All routes here require admin role
        $group->get('/users', [AdminApi::class, 'usersAction']);
        $group->get('/settings', [AdminApi::class, 'settingsAction']);
    });
```

### 3. requireRole($roles)
Requires authentication + specific role(s):

```php
// Single role
RouteGroup::create($app, '/editor')
    ->requireRole('editor')
    ->group(function ($group) {
        $group->get('/posts', [EditorApi::class, 'postsAction']);
    });

// Multiple roles (user needs ANY of these roles)
RouteGroup::create($app, '/staff')
    ->requireRole(['admin', 'editor', 'moderator'])
    ->group(function ($group) {
        $group->get('/dashboard', [StaffApi::class, 'dashboardAction']);
    });
```

## HTTP Methods

All standard HTTP methods are supported:

```php
RouteGroup::create($app, '/api')
    ->group(function ($group) {
        $group->get('/resource', $handler);      // GET
        $group->post('/resource', $handler);     // POST
        $group->put('/resource/{id}', $handler); // PUT
        $group->patch('/resource/{id}', $handler); // PATCH
        $group->delete('/resource/{id}', $handler); // DELETE
        $group->map('OPTIONS', '/resource', $handler); // Custom method
    });
```

## Custom Middleware

Add custom middleware to route groups:

```php
RouteGroup::create($app, '/api')
    ->middleware(function () {
        // Custom middleware logic
        header('X-API-Version: 1.0');
    })
    ->requireAuth()
    ->group(function ($group) {
        // Routes with custom middleware + auth
    });
```

## Current Implementation in app.php

The routes are now organized into logical groups:

### 1. Public Routes (No Authentication)
```php
RouteGroup::create($app)
    ->group(function ($group) {
        $group->get('/', function ($app) {
            // Home page
        });
    });
```

### 2. Public API Routes
```php
RouteGroup::create($app, '/api')
    ->group(function ($group) {
        // File upload, OAuth public endpoints, etc.
        $group->post('/v1/file', [(new FilepondApi()), "process"]);
        $group->get('/oauth/providers', [(new OAuth()), "providersAction"]);
    });
```

### 3. Authenticated API Routes
```php
RouteGroup::create($app, '/api')
    ->requireAuth()
    ->group(function ($group) {
        // Pusher auth, OAuth unlink, etc.
        $group->post('/pusher/auth', [(new PusherApi()), "authAction"]);
        $group->post('/oauth/unlink', [(new OAuth()), "unlinkAction"]);
    });
```

### 4. Admin API Routes
```php
RouteGroup::create($app, '/api')
    ->requireAdmin()
    ->group(function ($group) {
        // Role management API
        $group->get('/roles', [(new RolesApi()), "indexAction"]);
        $group->post('/roles', [(new RolesApi()), "createAction"]);
    });
```

### 5. Public Auth Routes
```php
RouteGroup::create($app, '/auth')
    ->group(function ($group) {
        // Login, register, password reset
        $group->post('/login', [(new Auth()), "loginAction"]);
        $group->post('/register', [(new Auth()), "registerAction"]);
    });
```

### 6. Protected Auth Routes
```php
RouteGroup::create($app, '/auth')
    ->requireAuth()
    ->group(function ($group) {
        // User profile, logout, etc.
        $group->get('/user', [(new Auth()), "userAction"]);
        $group->post('/logout', [(new Auth()), "logoutAction"]);
    });
```

## Error Responses

### Authentication Required (401)
```json
{
    "success": false,
    "message": "Authentication required",
    "error": "UNAUTHORIZED"
}
```

### Insufficient Permissions (403)
```json
{
    "success": false,
    "message": "Admin access required",
    "error": "FORBIDDEN"
}
```

## Benefits

### Before (Scattered Routes)
```php
// Scattered individual route definitions
$app->post('/api/pusher/auth', [(new PusherApi()), "authAction"]);
$app->post('/api/pusher/trigger', [(new PusherApi()), "triggerAction"]);
$app->get('/api/pusher/channels', [(new PusherApi()), "channelsAction"]);
// ... many more scattered routes
```

### After (Organized Groups)
```php
// Clean, organized route groups
RouteGroup::create($app, '/api')
    ->requireAuth()
    ->group(function ($group) {
        // All Pusher routes that need auth
        $group->post('/pusher/auth', [(new PusherApi()), "authAction"]);
        $group->post('/pusher/trigger', [(new PusherApi()), "triggerAction"]);
        $group->get('/pusher/channels', [(new PusherApi()), "channelsAction"]);
    });
```

## Advanced Usage Examples

### Nested Groups with Different Guards
```php
// Public API group
RouteGroup::create($app, '/api/v1')
    ->group(function ($group) {
        $group->get('/status', $publicHandler);
        
        // Nested authenticated group
        RouteGroup::create($group->getApp(), '/api/v1/user')
            ->requireAuth()
            ->group(function ($userGroup) {
                $userGroup->get('/profile', $profileHandler);
                $userGroup->post('/settings', $settingsHandler);
            });
    });
```

### Role-Based API Sections
```php
// Different API sections for different roles
RouteGroup::create($app, '/api/admin')
    ->requireAdmin()
    ->group(function ($group) {
        $group->get('/users', $adminUsersHandler);
        $group->get('/logs', $adminLogsHandler);
    });

RouteGroup::create($app, '/api/editor')
    ->requireRole('editor')
    ->group(function ($group) {
        $group->get('/posts', $editorPostsHandler);
        $group->post('/publish', $editorPublishHandler);
    });
```

### Custom Middleware Examples
```php
RouteGroup::create($app, '/api')
    ->middleware(function () {
        // Rate limiting
        if (!checkRateLimit()) {
            http_response_code(429);
            exit('Rate limit exceeded');
        }
    })
    ->middleware(function () {
        // CORS headers
        header('Access-Control-Allow-Origin: *');
    })
    ->requireAuth()
    ->group(function ($group) {
        // API routes with rate limiting + CORS + auth
    });
```

## Testing the Implementation

### Test Public Routes
```bash
curl http://localhost/
# Should return home page
```

### Test Protected Routes
```bash
# Without authentication - should return 401
curl http://localhost/api/pusher/auth

# With authentication - should work
curl -H "Authorization: Bearer YOUR_TOKEN" http://localhost/api/pusher/auth
```

### Test Admin Routes
```bash
# Without admin role - should return 403
curl -H "Authorization: Bearer USER_TOKEN" http://localhost/api/roles

# With admin role - should work
curl -H "Authorization: Bearer ADMIN_TOKEN" http://localhost/api/roles
```

## Migration from Old System

### Old Way (Individual Routes)
```php
$app->post('/auth/login', [(new Auth()), "loginAction"]);
$app->post('/auth/register', [(new Auth()), "registerAction"]);
$app->get('/auth/user', [(new Auth()), "userAction"]);
// ... scattered everywhere
```

### New Way (Organized Groups)
```php
// Public auth routes
RouteGroup::create($app, '/auth')
    ->group(function ($group) {
        $group->post('/login', [(new Auth()), "loginAction"]);
        $group->post('/register', [(new Auth()), "registerAction"]);
    });

// Protected auth routes
RouteGroup::create($app, '/auth')
    ->requireAuth()
    ->group(function ($group) {
        $group->get('/user', [(new Auth()), "userAction"]);
    });
```

## Security Notes

1. **Guards are applied at the group level** - All routes in a protected group are automatically protected
2. **Multiple guards can be chained** - `->requireAuth()->requireRole('admin')`
3. **Guards are checked before route execution** - Failed guards return appropriate HTTP status codes
4. **Middleware runs after guards** - Custom middleware only executes for authorized requests

## Future Enhancements

Potential future improvements:
- **Route caching** for better performance
- **Group-level rate limiting**
- **Automatic API documentation generation**
- **Request/response validation middleware**
- **Logging and monitoring middleware**