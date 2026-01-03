# JWT Authentication System

This document describes the JWT-based authentication system implemented in the Phalcon project stub.

## Overview

The authentication system has been upgraded from session-based authentication to JWT (JSON Web Token) based authentication, providing:

- **Stateless Authentication**: No server-side session storage required
- **API-First Design**: Perfect for REST APIs and single-page applications
- **Token Management**: Access tokens with refresh token capability
- **API Key Support**: Generate and manage API keys for programmatic access
- **Enhanced Security**: JWT tokens with configurable expiration times

## Architecture

### Backend Components

#### 1. JwtService (`IamLab\Service\Auth\JwtService`)

Core JWT functionality including:
- Access token generation (1 hour expiry)
- Refresh token generation (7 days expiry)
- Token validation and decoding
- API key generation and validation
- Token extraction from Authorization headers

#### 2. AuthService (`IamLab\Service\Auth\AuthService`)

Enhanced authentication service that:
- Integrates JWT tokens with existing session-based auth
- Provides backward compatibility
- Handles user authentication with JWT response
- Manages API key generation
- Supports token refresh functionality

#### 3. Auth API Controller (`IamLab\Service\Auth`)

REST API endpoints:
- `POST /api/auth/auth` - Login with email/password
- `POST /api/auth/register` - User registration
- `POST /api/auth/logout` - User logout
- `POST /api/auth/refresh-token` - Refresh access token
- `GET /api/auth/user` - Get current user info
- `GET /api/auth/profile` - Get user profile
- `POST /api/auth/update-profile` - Update user profile
- `POST /api/auth/generate-api-key` - Generate API key
- `POST /api/auth/forgot-password` - Password reset

### Frontend Components

#### JavaScript AuthService (`assets/js/services/AuthserviceService.js`)

Comprehensive client-side authentication service providing:
- Login/logout functionality
- Token management (localStorage)
- Automatic token refresh
- User profile management
- API key generation
- Authentication state management

## Usage

### Backend Usage

#### Basic Authentication

```php
use IamLab\Service\Auth\AuthService;
use IamLab\Model\User;

$authService = new AuthService();

// Authenticate user
$user = (new User())->setEmail($email)->setPassword($password);
$result = $authService->authenticate($user);

if ($result) {
    // $result contains:
    // - user: User object
    // - access_token: JWT access token
    // - refresh_token: JWT refresh token
    // - expires_in: Token expiration time
    // - token_type: "Bearer"
}
```

#### Token Validation

```php
// Check if user is authenticated (works with both JWT and session)
if ($authService->isAuthenticated()) {
    $user = $authService->getUser();
}

// Get user from JWT token
$token = $jwtService->extractTokenFromHeader();
$user = $authService->getUserFromToken($token);
```

#### API Key Management

```php
// Generate API key for user
$apiKey = $authService->generateApiKey($user);

// Validate API key
$user = $authService->validateApiKey($apiKey);
```

### Frontend Usage

#### Initialize Authentication Service

```javascript
import { AuthService } from './services/AuthserviceService.js';

// Initialize on page load
AuthService.init();
```

#### Login/Logout

```javascript
// Login
AuthService.login(email, password)
    .then(response => {
        console.log('Login successful', response);
        // User is now authenticated
    })
    .catch(error => {
        console.error('Login failed', error);
    });

// Logout
AuthService.logout()
    .then(() => {
        console.log('Logged out successfully');
    });
```

#### Check Authentication Status

```javascript
// Check if user is logged in
if (AuthService.isLoggedIn()) {
    const user = AuthService.getUser();
    console.log('Current user:', user);
}
```

#### Profile Management

```javascript
// Get user profile
AuthService.getProfile()
    .then(profile => {
        console.log('User profile:', profile);
    });

// Update profile
AuthService.updateProfile({ name: 'New Name' })
    .then(response => {
        console.log('Profile updated');
    });

// Generate API key
AuthService.generateApiKey()
    .then(response => {
        console.log('API key:', response.data.api_key);
    });
```

## Configuration

### Environment Variables

Add to your `.env` file:

```env
# JWT Secret Key (REQUIRED)
JWT_SECRET=your-super-secret-jwt-key-change-this-in-production

# JWT Token Expiry Configuration (OPTIONAL)
JWT_ACCESS_TOKEN_EXPIRY=3600      # Access token expiry in seconds (default: 1 hour)
JWT_REFRESH_TOKEN_EXPIRY=604800   # Refresh token expiry in seconds (default: 7 days)

# Additional JWT Configuration (OPTIONAL)
JWT_ALGORITHM=HS256               # JWT signing algorithm (default: HS256)
JWT_ISSUER=phalcon-stub          # Token issuer (default: phalcon-stub)
JWT_AUDIENCE=phalcon-stub-users  # Token audience (default: phalcon-stub-users)

# Frontend auth behavior (backend-controlled)
# If set to 0 or -1, the frontend will NOT auto-logout on inactivity
AUTH_CLIENT_INACTIVITY_TIMEOUT_MINUTES=30
# How often the client checks/refreshes token, in minutes (default 5)
AUTH_CLIENT_TOKEN_CHECK_INTERVAL_MINUTES=5
```

**Important**: Change the JWT secret in production for security.

### Token Expiration Configuration

You can customize JWT token expiry times by setting environment variables:

#### Access Token Expiry (`JWT_ACCESS_TOKEN_EXPIRY`)
- **Default**: 3600 seconds (1 hour)
- **Purpose**: Short-lived tokens for API access
- **Recommended Range**: 300-7200 seconds (5 minutes to 2 hours)

**Examples:**
```env
JWT_ACCESS_TOKEN_EXPIRY=1800    # 30 minutes
JWT_ACCESS_TOKEN_EXPIRY=7200    # 2 hours
JWT_ACCESS_TOKEN_EXPIRY=300     # 5 minutes (high security)
```

#### Refresh Token Expiry (`JWT_REFRESH_TOKEN_EXPIRY`)
- **Default**: 604800 seconds (7 days)
- **Purpose**: Long-lived tokens for renewing access tokens
- **Recommended Range**: 86400-2592000 seconds (1 day to 30 days)

**Examples:**
```env
JWT_REFRESH_TOKEN_EXPIRY=86400     # 1 day
JWT_REFRESH_TOKEN_EXPIRY=1209600   # 14 days
JWT_REFRESH_TOKEN_EXPIRY=2592000   # 30 days
```

#### Security Considerations for Token Expiry

**Shorter Access Tokens (More Secure):**
- ✅ Reduced exposure window if token is compromised
- ✅ Better for high-security applications
- ❌ More frequent token refresh requests
- ❌ Potential user experience interruptions

**Longer Access Tokens (More Convenient):**
- ✅ Fewer refresh requests
- ✅ Better user experience
- ❌ Longer exposure window if compromised
- ❌ Less secure for sensitive applications

**Recommended Configurations:**

**High Security Applications:**
```env
JWT_ACCESS_TOKEN_EXPIRY=900     # 15 minutes
JWT_REFRESH_TOKEN_EXPIRY=86400  # 1 day
```

**Standard Applications:**
```env
JWT_ACCESS_TOKEN_EXPIRY=3600    # 1 hour (default)
JWT_REFRESH_TOKEN_EXPIRY=604800 # 7 days (default)
```

**Development/Testing:**
```env
JWT_ACCESS_TOKEN_EXPIRY=7200    # 2 hours
JWT_REFRESH_TOKEN_EXPIRY=1209600 # 14 days
```

### Long‑Lived Sessions ("Stay logged in")

To minimize logouts while keeping security reasonable:

- Disable inactivity auto-logout on the client via backend env:
  ```env
  AUTH_CLIENT_INACTIVITY_TIMEOUT_MINUTES=0
  ```
  A value of `0` or `-1` disables inactivity‑based logout in the browser.

- Increase refresh token lifetime and keep a moderate access token lifetime:
  ```env
  JWT_ACCESS_TOKEN_EXPIRY=86400        # 24 hours access token
  JWT_REFRESH_TOKEN_EXPIRY=31536000    # ~1 year refresh token
  ```

The frontend polls `/auth/config` at startup to read these values. It will keep the user signed in by refreshing the access token periodically as long as the refresh token is valid.

Note: Extremely long refresh token lifetimes increase risk if the token is exfiltrated. Consider device revocation and rotate secrets periodically in production.

#### API Keys
- **Expiration**: No expiration by default
- **Purpose**: Long-term programmatic access
- **Security**: Can be revoked manually through the API

## API Endpoints

### Authentication Endpoints

#### POST /api/auth/auth
Login with email and password.

**Request:**
```json
{
    "email": "user@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com"
        },
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "expires_in": 3600,
        "token_type": "Bearer"
    }
}
```

#### POST /api/auth/refresh-token
Refresh access token using refresh token.

**Request:**
```json
{
    "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
}
```

**Response:**
```json
{
    "success": true,
    "message": "Token refreshed successfully",
    "data": {
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "expires_in": 3600,
        "token_type": "Bearer"
    }
}
```

### Profile Management Endpoints

#### GET /api/auth/profile
Get user profile information.

**Headers:**
```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "api_key": "***12345678"
    }
}
```

#### POST /api/auth/generate-api-key
Generate new API key for authenticated user.

**Headers:**
```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

**Response:**
```json
{
    "success": true,
    "message": "API key generated successfully",
    "data": {
        "api_key": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
    }
}
```

## Security Considerations

1. **JWT Secret**: Always use a strong, unique JWT secret in production
2. **Token Storage**: Access tokens are stored in localStorage (consider httpOnly cookies for enhanced security)
3. **Token Expiration**: Short-lived access tokens with refresh token mechanism
4. **API Keys**: Stored in database and can be regenerated
5. **HTTPS**: Always use HTTPS in production to protect tokens in transit

## Migration from Session-Based Auth

The system maintains backward compatibility with session-based authentication:

1. Existing session-based authentication continues to work
2. JWT tokens take precedence when present
3. `getIdentity()` method checks JWT first, falls back to session
4. Gradual migration possible without breaking existing functionality

## Testing

### Manual Testing

1. **Login Test:**
   ```bash
   curl -X POST http://localhost:8080/api/auth/auth \
     -H "Content-Type: application/json" \
     -d '{"email":"admin@example.com","password":"password"}'
   ```

2. **Profile Test:**
   ```bash
   curl -X GET http://localhost:8080/api/auth/profile \
     -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
   ```

3. **API Key Generation:**
   ```bash
   curl -X POST http://localhost:8080/api/auth/generate-api-key \
     -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
   ```

### Frontend Testing

1. Open browser console
2. Import and initialize AuthService
3. Test login, profile management, and API key generation
4. Verify token persistence across page reloads

## Troubleshooting

### Common Issues

1. **"Invalid token signature"**: Check JWT_SECRET configuration
2. **"Token has expired"**: Use refresh token to get new access token
3. **"Authentication required"**: Ensure Authorization header is properly set
4. **localStorage issues**: Check browser privacy settings

### Debug Mode

Enable debug mode by checking the `debug` field in API responses during development.

## Future Enhancements

- [ ] Token blacklisting for logout
- [ ] Multiple API keys per user
- [ ] Role-based access control (RBAC)
- [ ] Two-factor authentication (2FA)
- [ ] OAuth integration
- [ ] Rate limiting for API endpoints