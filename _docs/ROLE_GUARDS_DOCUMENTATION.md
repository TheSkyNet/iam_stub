# Role-Based Guards Implementation

This document describes the complete role-based authentication and authorization system implemented for both frontend and backend components.

## Overview

The system provides comprehensive role-based access control with:
- **Frontend Guards**: JavaScript functions that protect routes based on authentication and roles
- **Backend Guards**: PHP methods that protect API endpoints based on authentication and roles
- **Role Management**: Complete CRUD operations for managing user roles

## Frontend Implementation

### AuthService Role Methods

The frontend `AuthService` has been enhanced with role checking methods:

```javascript
// Check if user has a specific role
AuthService.hasRole('admin') // returns boolean

// Check if user has any of the specified roles
AuthService.hasAnyRole(['admin', 'editor']) // returns boolean

// Check if user has all of the specified roles
AuthService.hasAllRoles(['admin', 'member']) // returns boolean

// Check if user is an admin
AuthService.isAdmin() // returns boolean

// Get all user roles
AuthService.getUserRoles() // returns array of role names
```

### Guard Functions

Three guard functions are available for protecting frontend routes:

#### 1. authGuard(component)
Protects routes requiring only authentication (existing functionality):
```javascript
"/profile": layout(authGuard(Profile))
```

#### 2. adminGuard(component)
Protects routes requiring authentication + admin role:
```javascript
"/admin": layout(adminGuard(AdminPanel))
"/admin/roles": layout(adminGuard(RoleManagement))
```

#### 3. roleGuard(component, requiredRoles)
Protects routes requiring authentication + specific role(s):
```javascript
// Single role
"/editor": layout(roleGuard(EditorPanel, 'editor'))

// Multiple roles (user needs ANY of these roles)
"/staff": layout(roleGuard(StaffPanel, ['admin', 'editor']))
```

### Frontend Route Examples

```javascript
m.route(root, "/", {
    "/": layout(Welcome),
    "/login": layout(LoginForm),
    "/profile": layout(authGuard(Profile)),
    
    // Admin-only routes
    "/admin": layout(adminGuard(AdminPanel)),
    "/admin/roles": layout(adminGuard(RoleManagement)),
    
    // Role-specific routes
    "/editor": layout(roleGuard(EditorPanel, 'editor')),
    "/member": layout(roleGuard(MemberArea, 'member')),
    "/staff": layout(roleGuard(StaffArea, ['admin', 'editor']))
});
```

## Backend Implementation

### API Base Class Guards

The `aAPI` base class provides role-based guard methods for protecting API endpoints:

#### 1. requireAuth()
Requires user authentication:
```php
protected function someAction(): void
{
    $this->requireAuth();
    // Your protected code here
}
```

#### 2. requireAdmin()
Requires authentication + admin role:
```php
protected function adminOnlyAction(): void
{
    $this->requireAdmin();
    // Admin-only code here
}
```

#### 3. requireRole($roles)
Requires authentication + specific role(s):
```php
// Single role
protected function editorAction(): void
{
    $this->requireRole('editor');
    // Editor-only code here
}

// Multiple roles (user needs ANY of these roles)
protected function staffAction(): void
{
    $this->requireRole(['admin', 'editor']);
    // Staff-only code here
}
```

#### 4. requireAllRoles($roles)
Requires authentication + ALL specified roles:
```php
protected function superUserAction(): void
{
    $this->requireAllRoles(['admin', 'member']);
    // Code requiring both admin AND member roles
}
```

#### 5. getCurrentUser()
Gets the currently authenticated user:
```php
protected function someAction(): void
{
    $this->requireAuth();
    $user = $this->getCurrentUser();
    // Use $user object
}
```

### RolesApi Protection

The `RolesApi` endpoints are now protected with admin guards:

```php
// All role management endpoints require admin access
public function indexAction(): void    // GET /api/roles
public function showAction(): void     // GET /api/roles/:id
public function createAction(): void   // POST /api/roles
public function updateAction(): void   // PUT /api/roles/:id
public function deleteAction(): void   // DELETE /api/roles/:id
public function searchAction(): void   // GET /api/roles/search
```

## Backend User Data Structure

The authentication system now includes roles in all responses:

```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "Admin User",
            "email": "admin@example.com",
            "roles": ["admin", "member"]
        },
        "access_token": "...",
        "refresh_token": "...",
        "expires_in": 3600,
        "token_type": "Bearer"
    }
}
```

## Error Responses

### Frontend
- **Authentication Required**: Redirects to `/login`
- **Insufficient Permissions**: Redirects to `/` (home page)

### Backend
- **Authentication Required**: HTTP 401 with error response
- **Insufficient Permissions**: HTTP 403 with error response

```json
// 401 Unauthorized
{
    "success": false,
    "message": "Authentication required",
    "error": "UNAUTHORIZED"
}

// 403 Forbidden
{
    "success": false,
    "message": "Admin access required",
    "error": "FORBIDDEN"
}
```

## Testing the Implementation

### Frontend Testing
1. **Login as admin user**: `admin@test.com` / `admin123`
2. **Test routes**:
   - `/profile` - Should work (auth required)
   - `/admin` - Should work (admin required)
   - `/editor` - Should redirect to home (admin doesn't have editor role)
   - `/member` - Should work if admin has member role

### Backend Testing
1. **Test API endpoints** with different user roles:
   ```bash
   # Test without authentication
   curl -X GET http://localhost/api/roles
   # Should return 401

   # Test with admin token
   curl -X GET http://localhost/api/roles \
     -H "Authorization: Bearer YOUR_ADMIN_TOKEN"
   # Should return roles list

   # Test with non-admin token
   curl -X GET http://localhost/api/roles \
     -H "Authorization: Bearer YOUR_USER_TOKEN"
   # Should return 403
   ```

## Usage Examples

### Protecting a New Frontend Route
```javascript
// Add to app.js routes
"/my-admin-page": layout(adminGuard(MyAdminComponent))
"/my-editor-page": layout(roleGuard(MyEditorComponent, 'editor'))
```

### Protecting a New API Endpoint
```php
// In your API controller
public function sensitiveAction(): void
{
    $this->requireRole(['admin', 'editor']);
    
    // Your protected logic here
    $this->dispatch([
        'success' => true,
        'data' => 'Sensitive data'
    ]);
}
```

### Checking Roles in Components
```javascript
// In your Mithril component
view: function() {
    return m('div', [
        m('h1', 'Dashboard'),
        
        // Show admin-only content
        AuthService.isAdmin() ? 
            m('button', 'Admin Panel') : null,
            
        // Show editor content
        AuthService.hasRole('editor') ? 
            m('button', 'Edit Content') : null,
            
        // Show content for multiple roles
        AuthService.hasAnyRole(['admin', 'editor']) ? 
            m('button', 'Staff Tools') : null
    ]);
}
```

## Security Notes

1. **Frontend guards are for UX only** - Always validate permissions on the backend
2. **Backend guards are the real security** - They prevent unauthorized API access
3. **Roles are included in JWT tokens** - No additional database queries needed for role checks
4. **Session compatibility** - System works with both JWT and session-based authentication

## Available Roles

The system comes with four default roles:
- **admin**: Full system access
- **editor**: Content management permissions
- **member**: Standard user access
- **guest**: Limited access to public content

Additional roles can be created using the role management system or the `user:add-role` command.