# OAuth Integration Documentation

## Overview

This document describes the OAuth integration implementation for the IAMLab application. The OAuth system supports multiple providers and can be easily toggled on/off via environment variables.

## Supported Providers

- **Google OAuth 2.0**
- **GitHub OAuth**
- **Facebook OAuth**
- **Generic OAuth 2.0** (for custom providers)

## Quick Navigation

**🚀 Jump to Setup Guides:**
- [📱 Google OAuth Setup](#google-oauth-setup) - Get Google Client ID & Secret
- [🐙 GitHub OAuth Setup](#github-oauth-setup) - Create GitHub OAuth App
- [📘 Facebook OAuth Setup](#facebook-oauth-setup) - Configure Facebook Login
- [⚙️ Generic OAuth Setup](#generic-oauth-20-provider-setup) - Custom Providers

**🔧 Configuration:**
- [Environment Variables](#environment-variables) - Copy-paste .env configuration
- [Backend Implementation](#backend-implementation) - Technical details
- [Frontend Integration](#frontend-implementation) - JavaScript components

**🆘 Need Help?**
- [Visual Troubleshooting](#visual-troubleshooting-guide) - Common UI issues
- [Common Issues](#common-issues) - Frequent problems and solutions

## Features

- ✅ Toggleable OAuth providers via environment variables
- ✅ Secure state parameter validation
- ✅ User account creation and linking
- ✅ Frontend integration with login form
- ✅ Account management (link/unlink providers)
- ✅ Comprehensive error handling
- ✅ Support for custom OAuth 2.0 providers

## Configuration

### Environment Variables

Add the following variables to your `.env` file:

```env
# OAuth Configuration
OAUTH_ENABLED=false
OAUTH_REDIRECT_URI=/auth/oauth/callback

# Google OAuth Configuration
OAUTH_GOOGLE_ENABLED=false
OAUTH_GOOGLE_CLIENT_ID=your_google_client_id
OAUTH_GOOGLE_CLIENT_SECRET=your_google_client_secret
OAUTH_GOOGLE_REDIRECT_URI=/auth/oauth/google/callback

# GitHub OAuth Configuration
OAUTH_GITHUB_ENABLED=false
OAUTH_GITHUB_CLIENT_ID=your_github_client_id
OAUTH_GITHUB_CLIENT_SECRET=your_github_client_secret
OAUTH_GITHUB_REDIRECT_URI=/auth/oauth/github/callback

# Facebook OAuth Configuration
OAUTH_FACEBOOK_ENABLED=false
OAUTH_FACEBOOK_CLIENT_ID=your_facebook_app_id
OAUTH_FACEBOOK_CLIENT_SECRET=your_facebook_app_secret
OAUTH_FACEBOOK_REDIRECT_URI=/auth/oauth/facebook/callback

# Generic OAuth2 Configuration
OAUTH_GENERIC_ENABLED=false
OAUTH_GENERIC_CLIENT_ID=your_client_id
OAUTH_GENERIC_CLIENT_SECRET=your_client_secret
OAUTH_GENERIC_REDIRECT_URI=/auth/oauth/generic/callback
OAUTH_GENERIC_AUTHORIZATION_URL=https://provider.com/oauth/authorize
OAUTH_GENERIC_TOKEN_URL=https://provider.com/oauth/token
OAUTH_GENERIC_USER_INFO_URL=https://provider.com/api/user
OAUTH_GENERIC_SCOPES=openid,profile,email
```

### Provider Setup

> **📸 Visual Guide Note**: This section includes detailed step-by-step instructions with visual landmarks to help you navigate each platform's interface. Look for the specific UI elements mentioned in each step.

#### Google OAuth Setup

**Step 1: Access Google Cloud Console**
1. Navigate to [Google Cloud Console](https://console.cloud.google.com/)
2. **Visual Landmark**: Look for the blue "Google Cloud" header at the top
3. Sign in with your Google account if prompted

**Step 2: Create or Select Project**
1. **Visual Landmark**: Find the project dropdown in the top navigation bar (usually shows "My Project" or current project name)
2. Click the project dropdown next to the Google Cloud logo
3. **Screenshot Reference**: You'll see a modal with "SELECT A PROJECT" at the top
4. Either:
   - Click "NEW PROJECT" button (top right of modal) to create new project
   - Select an existing project from the list

**Step 3: Enable Required APIs**
1. **Visual Landmark**: Look for the hamburger menu (☰) in the top-left corner
2. Navigate to "APIs & Services" > "Library"
3. **Screenshot Reference**: You'll see a search bar with "Search for APIs & Services"
4. Search for "Google+ API" or "People API"
5. Click on the API result and click the blue "ENABLE" button
6. **Alternative**: Also enable "Google Identity" API for better integration

**Step 4: Create OAuth 2.0 Credentials**
1. Go to "APIs & Services" > "Credentials" (from the left sidebar)
2. **Visual Landmark**: Look for the "+ CREATE CREDENTIALS" button at the top
3. Click "CREATE CREDENTIALS" > "OAuth client ID"
4. **Screenshot Reference**: You'll see a form titled "Create OAuth client ID"
5. Select "Web application" as the application type
6. **Important Visual Cue**: The form will expand to show additional fields

**Step 5: Configure OAuth Client**
1. **Application name**: Enter a descriptive name (e.g., "IAMLab OAuth")
2. **Authorized JavaScript origins**: Add your domain origins:
   - `http://localhost:8080` (for development)
   - `https://yourdomain.com` (for production)
3. **Authorized redirect URIs**: Add these exact URLs:
   - `http://localhost:8080/auth/oauth/google/callback` (development)
   - `https://yourdomain.com/auth/oauth/google/callback` (production)
4. **Visual Landmark**: Look for the blue "CREATE" button at the bottom
5. **Screenshot Reference**: After creation, you'll see a modal with your Client ID and Client Secret

**Step 6: Copy Credentials**
1. **Visual Landmark**: Look for the "OAuth 2.0 Client IDs" section in the credentials list
2. Click the pencil/edit icon next to your newly created OAuth client
3. **Screenshot Reference**: Copy the "Client ID" and "Client secret" values
4. Add these to your `.env` file as `OAUTH_GOOGLE_CLIENT_ID` and `OAUTH_GOOGLE_CLIENT_SECRET`

---

#### GitHub OAuth Setup

**Step 1: Access GitHub Developer Settings**
1. Go to [GitHub.com](https://github.com/settings/developers) and sign in 
**Step 3: Create New OAuth App**
1. **Visual Landmark**: Look for the green "New OAuth App" button (top-right area)
2. Click "New OAuth App"
3. **Screenshot Reference**: You'll see a form titled "Register a new OAuth application"

**Step 4: Fill Application Details**
1. **Application name**: Enter a descriptive name (e.g., "IAMLab Authentication")
2. **Homepage URL**: Enter your application's homepage:
   - `http://localhost:8080` (development)
   - `https://yourdomain.com` (production)
3. **Application description**: Optional, but helpful for identification
4. **Authorization callback URL**: Enter the exact callback URL:
   - `http://localhost:8080/auth/oauth/github/callback` (development)
   - `https://yourdomain.com/auth/oauth/github/callback` (production)
5. **Visual Landmark**: Look for the green "Register application" button at the bottom

**Step 5: Get Client Credentials**
1. **Screenshot Reference**: After registration, you'll see the app details page
2. **Visual Landmark**: Look for "Client ID" (visible immediately)
3. **Client Secret**: Click "Generate a new client secret" button
4. **Important**: Copy both values immediately - the secret won't be shown again
5. Add these to your `.env` file as `OAUTH_GITHUB_CLIENT_ID` and `OAUTH_GITHUB_CLIENT_SECRET`

**Step 6: Configure Permissions (Optional)**
1. **Visual Landmark**: Look for the "Permissions & events" section
2. **Screenshot Reference**: You can configure which user data your app can access
3. For basic authentication, the default permissions are sufficient

---

#### Facebook OAuth Setup

**Step 1: Access Facebook Developers**
1. Navigate to [Facebook Developers](https://developers.facebook.com/)
2. **Visual Landmark**: Look for the blue Facebook header with "for Developers"
3. Click "Get Started" or "My Apps" if you already have an account
4. Sign in with your Facebook account

**Step 2: Create New App**
1. **Visual Landmark**: Look for the green "+ Create App" button (usually top-right)
2. Click "Create App"
3. **Screenshot Reference**: You'll see a modal titled "Create an App"
4. Select "Consumer" or "Business" based on your use case
5. Click "Next"

**Step 3: Configure Basic App Info**
1. **App Display Name**: Enter your application name (e.g., "IAMLab")
2. **App Contact Email**: Enter a valid contact email
3. **Visual Landmark**: Look for the blue "Create App" button
4. Complete any security verification if prompted

**Step 4: Add Facebook Login Product**
1. **Visual Landmark**: Look for the left sidebar with "PRODUCTS" section
2. **Screenshot Reference**: You'll see various product options like "Facebook Login", "Analytics", etc.
3. Find "Facebook Login" and click the "Set Up" button
4. **Alternative Path**: If not visible, click "Add Product" and search for "Facebook Login"

**Step 5: Configure Facebook Login Settings**
1. **Visual Landmark**: In the left sidebar, under "Facebook Login", click "Settings"
2. **Screenshot Reference**: You'll see "Client OAuth Settings" section
3. **Valid OAuth Redirect URIs**: Add these exact URLs (one per line):
   - `http://localhost:8080/auth/oauth/facebook/callback`
   - `https://yourdomain.com/auth/oauth/facebook/callback`
4. **Visual Landmark**: Look for the blue "Save Changes" button at the bottom

**Step 6: Get App Credentials**
1. **Visual Landmark**: In the left sidebar, click "Settings" > "Basic"
2. **Screenshot Reference**: You'll see "App ID" and "App Secret" fields
3. **App ID**: Copy this value (visible by default)
4. **App Secret**: Click "Show" next to the App Secret field, enter your Facebook password
5. **Important**: Copy both values immediately
6. Add these to your `.env` file as `OAUTH_FACEBOOK_CLIENT_ID` and `OAUTH_FACEBOOK_CLIENT_SECRET`

**Step 7: Configure App Domain (Important)**
1. **Visual Landmark**: Still in "Settings" > "Basic"
2. **App Domains**: Add your domain without protocol:
   - `localhost` (for development)
   - `yourdomain.com` (for production)
3. **Privacy Policy URL**: Required for public apps
4. **Terms of Service URL**: Recommended

---

#### Generic OAuth 2.0 Provider Setup

**For Custom OAuth Providers (e.g., Keycloak, Auth0, Okta):**

**Step 1: Identify Required URLs**
Most OAuth 2.0 providers require these endpoints:
1. **Authorization URL**: Where users are redirected to login
2. **Token URL**: Where authorization codes are exchanged for tokens
3. **User Info URL**: Where user profile information is retrieved

**Step 2: Common Provider Patterns**

**Auth0 Example:**
- **Visual Landmark**: Look for "Applications" in the Auth0 dashboard
- Authorization URL: `https://your-domain.auth0.com/authorize`
- Token URL: `https://your-domain.auth0.com/oauth/token`
- User Info URL: `https://your-domain.auth0.com/userinfo`

**Keycloak Example:**
- **Visual Landmark**: Look for "Clients" in the Keycloak admin console
- Authorization URL: `https://your-keycloak.com/auth/realms/your-realm/protocol/openid-connect/auth`
- Token URL: `https://your-keycloak.com/auth/realms/your-realm/protocol/openid-connect/token`
- User Info URL: `https://your-keycloak.com/auth/realms/your-realm/protocol/openid-connect/userinfo`

**Step 3: Configure Redirect URIs**
Add these callback URLs in your provider's settings:
- `http://localhost:8080/auth/oauth/generic/callback` (development)
- `https://yourdomain.com/auth/oauth/generic/callback` (production)

**Step 4: Update Environment Variables**
```env
OAUTH_GENERIC_ENABLED=true
OAUTH_GENERIC_CLIENT_ID=your_client_id
OAUTH_GENERIC_CLIENT_SECRET=your_client_secret
OAUTH_GENERIC_AUTHORIZATION_URL=https://provider.com/oauth/authorize
OAUTH_GENERIC_TOKEN_URL=https://provider.com/oauth/token
OAUTH_GENERIC_USER_INFO_URL=https://provider.com/api/user
OAUTH_GENERIC_SCOPES=openid,profile,email
```

## Backend Implementation

### Service Classes

#### Base OAuth Service (`IamLab\Service\Auth\OAuthService`)

Abstract base class that provides common OAuth 2.0 functionality:

- State parameter generation and validation
- User creation and updating
- HTTP request handling
- Configuration management

#### Provider-Specific Services

- `GoogleOAuthService` - Google OAuth 2.0 implementation
- `GitHubOAuthService` - GitHub OAuth implementation
- `FacebookOAuthService` - Facebook OAuth implementation
- `GenericOAuthService` - Generic OAuth 2.0 implementation

#### OAuth Controller (`IamLab\Service\OAuth`)

Handles OAuth flow endpoints:

- `GET /api/oauth/providers` - Get available providers
- `GET /api/oauth/redirect?provider=google` - Initiate OAuth flow
- `GET /api/oauth/callback?provider=google&code=...&state=...` - Handle OAuth callback
- `POST /api/oauth/unlink?provider=google` - Unlink OAuth provider

### Database Schema

The User model includes the following OAuth-related fields:

```php
protected $oauth_provider;    // string(50) - Provider name (google, github, etc.)
protected $oauth_id;          // string(255) - Provider user ID
protected $avatar;            // string(255) - User avatar URL
protected $email_verified;    // boolean - Email verification status
protected $created_at;        // datetime - Account creation timestamp
protected $updated_at;        // datetime - Last update timestamp
```

## Frontend Implementation

### JavaScript Services

#### OAuth Service (`assets/js/services/OAuthService.js`)

Provides frontend OAuth functionality:

- `getProviders()` - Fetch available OAuth providers
- `initiateLogin(provider)` - Start OAuth flow
- `handleCallback(provider, code, state)` - Handle OAuth callback
- `unlinkProvider(provider)` - Unlink OAuth provider
- `getProviderConfig(provider)` - Get UI configuration for provider

### Components

#### OAuth Buttons (`assets/js/components/OAuthButtons.js`)

- `OAuthButtons` - Displays OAuth login buttons
- `OAuthCallback` - Handles OAuth callback processing
- `OAuthAccountManager` - Manages linked OAuth accounts

### Integration

OAuth buttons are automatically integrated into the login form when OAuth is enabled. The buttons appear below the traditional email/password login form.

## Security Features

### State Parameter Validation

Each OAuth request includes a cryptographically secure state parameter that is:
- Generated using `bin2hex(random_bytes(16))`
- Stored in the user's session
- Validated on callback to prevent CSRF attacks

### Token Security

- Access tokens are stored securely in localStorage
- Refresh tokens are handled server-side
- All OAuth requests use HTTPS in production

### User Account Security

- OAuth accounts are linked to existing users by email
- Users cannot unlink their only authentication method without setting a password
- Email verification is automatically set for OAuth users

## Usage Examples

### Enable Google OAuth

1. Set up Google OAuth application
2. Update `.env` file:
   ```env
   OAUTH_ENABLED=true
   OAUTH_GOOGLE_ENABLED=true
   OAUTH_GOOGLE_CLIENT_ID=your_client_id
   OAUTH_GOOGLE_CLIENT_SECRET=your_client_secret
   ```
3. OAuth buttons will automatically appear on the login form

### Custom OAuth Provider

For custom OAuth 2.0 providers, use the generic configuration:

```env
OAUTH_GENERIC_ENABLED=true
OAUTH_GENERIC_CLIENT_ID=your_client_id
OAUTH_GENERIC_CLIENT_SECRET=your_client_secret
OAUTH_GENERIC_AUTHORIZATION_URL=https://provider.com/oauth/authorize
OAUTH_GENERIC_TOKEN_URL=https://provider.com/oauth/token
OAUTH_GENERIC_USER_INFO_URL=https://provider.com/api/user
OAUTH_GENERIC_SCOPES=openid,profile,email
```

The generic service automatically maps common field names from the user info response.

## API Endpoints

### Get Available Providers

```http
GET /api/oauth/providers
```

Response:
```json
{
  "success": true,
  "providers": [
    {
      "name": "google",
      "display_name": "Google",
      "auth_url": "/auth/oauth/google"
    }
  ]
}
```

### Initiate OAuth Flow

```http
GET /api/oauth/redirect?provider=google
```

Response:
```json
{
  "success": true,
  "auth_url": "https://accounts.google.com/o/oauth2/v2/auth?client_id=...",
  "state": "abc123..."
}
```

### OAuth Callback

```http
GET /api/oauth/callback?provider=google&code=auth_code&state=abc123
```

Response:
```json
{
  "success": true,
  "message": "OAuth authentication successful",
  "user": {
    "id": 1,
    "email": "user@example.com",
    "name": "John Doe",
    "avatar": "https://..."
  },
  "auth": {
    "access_token": "jwt_token",
    "refresh_token": "refresh_token"
  }
}
```

## Error Handling

The OAuth system includes comprehensive error handling:

- Invalid state parameters
- Missing OAuth configuration
- Provider API errors
- Network connectivity issues
- User permission denials

All errors are logged and user-friendly messages are displayed.

## Testing

To test the OAuth integration:

1. Set up OAuth applications with providers
2. Configure environment variables
3. Enable OAuth in configuration
4. Test the OAuth flow through the web interface
5. Verify user account creation and linking
6. Test account management features

## Visual Troubleshooting Guide

> **👀 Visual Debugging**: This section helps you identify and resolve issues by looking at specific visual cues in your browser and the OAuth provider interfaces.

### 🔍 OAuth Buttons Not Appearing

**What to Look For:**
- **Expected**: OAuth buttons should appear below the email/password form with provider logos
- **Visual Check**: Look for buttons with text like "Continue with Google", "Continue with GitHub"

**Visual Debugging Steps:**
1. **Check Browser Console** (F12 → Console tab):
   - **Red errors**: Look for JavaScript errors mentioning "OAuth" or "undefined"
   - **Network tab**: Check if `/api/oauth/providers` request returns data

2. **Check Login Form**:
   - **Visual Landmark**: OAuth buttons should appear after the "Sign In" button
   - **Missing buttons**: Usually indicates OAuth is disabled or misconfigured

**Quick Visual Fixes:**
```bash
# Check if OAuth is enabled in your .env file
grep "OAUTH_ENABLED" .env
# Should show: OAUTH_ENABLED=true

# Check if at least one provider is enabled
grep "OAUTH_.*_ENABLED=true" .env
```

### 🚫 OAuth Redirect Errors

**What You'll See:**
- **Error Page**: "redirect_uri_mismatch" or similar OAuth error
- **URL Bar**: Check the current URL for error parameters

**Visual Debugging:**
1. **Check the Error URL**:
   - Look for `?error=redirect_uri_mismatch` in the address bar
   - Note the `redirect_uri` parameter value

2. **Compare with Provider Settings**:
   - **Google**: Go to Google Cloud Console → Credentials → Your OAuth Client
   - **GitHub**: Go to GitHub → Settings → Developer settings → OAuth Apps → Your App
   - **Facebook**: Go to Facebook Developers → Your App → Facebook Login → Settings

**Visual Verification Checklist:**
- ✅ Redirect URI in provider settings **exactly matches** your callback URL
- ✅ Protocol matches (http vs https)
- ✅ Port number matches (if using localhost:8080)
- ✅ No trailing slashes or extra characters

### 🔐 Missing Client ID/Secret Errors

**What You'll See:**
- **Console Error**: "OAuth provider 'google' is not enabled" 
- **Network Error**: 401 Unauthorized responses
- **Login Failure**: "Failed to initiate OAuth login" message

**Visual Debugging Steps:**
1. **Check Environment Variables**:
   ```bash
   # Visual check - these should NOT be empty
   echo "Google Client ID: $OAUTH_GOOGLE_CLIENT_ID"
   echo "Google Secret: $OAUTH_GOOGLE_CLIENT_SECRET"
   ```

2. **Provider Dashboard Visual Check**:
   - **Google**: Look for "Client ID" and "Client secret" in OAuth 2.0 Client IDs section
   - **GitHub**: Look for "Client ID" and generate new "Client secret" if needed
   - **Facebook**: Look for "App ID" and "App Secret" in Basic Settings

### 🌐 CORS and Network Issues

**What You'll See:**
- **Console Error**: "CORS policy" or "blocked by CORS policy"
- **Network Tab**: Failed requests with status 0 or CORS errors

**Visual Debugging:**
1. **Check Request URLs**:
   - **Visual Landmark**: In Network tab, look for requests to `/api/oauth/`
   - **Status Codes**: 200 = success, 4xx/5xx = errors

2. **Domain Configuration**:
   - **Google**: Check "Authorized JavaScript origins" includes your domain
   - **Facebook**: Check "App Domains" includes your domain (without protocol)

### 📱 Mobile/Responsive Issues

**What You'll See:**
- **OAuth buttons**: Too wide, overlapping, or not clickable on mobile
- **Provider login pages**: Not mobile-optimized

**Visual Fixes:**
1. **Check Button Styling**:
   - **Expected**: Buttons should be full-width and properly spaced
   - **CSS Check**: Look for `width: 100%` and proper margins

2. **Test on Different Screen Sizes**:
   - **Browser DevTools**: Toggle device toolbar (Ctrl+Shift+M)
   - **Visual Check**: Buttons should remain clickable and properly sized

### 🔄 State Parameter Validation Errors

**What You'll See:**
- **Error Message**: "Invalid OAuth state parameter"
- **Console Warning**: State mismatch errors

**Visual Debugging:**
1. **Check Browser Storage**:
   - **DevTools**: Application tab → Session Storage
   - **Look for**: `oauth_state` and `oauth_provider` entries
   - **Visual Check**: Values should match between storage and URL parameters

2. **Clear Browser Data**:
   - **Visual Fix**: Clear cookies and session storage for your domain
   - **Shortcut**: Ctrl+Shift+Delete → Clear browsing data

### 🎨 Provider-Specific Visual Issues

#### Google OAuth Visual Cues
- **✅ Success**: Blue "Google" button with Google logo
- **❌ Problem**: Generic button or missing Google branding
- **Fix**: Check if Google Identity API is enabled

#### GitHub OAuth Visual Cues  
- **✅ Success**: Dark button with GitHub Octocat logo
- **❌ Problem**: Button appears but login fails
- **Fix**: Verify email scope permissions in GitHub app settings

#### Facebook OAuth Visual Cues
- **✅ Success**: Blue button with Facebook "f" logo
- **❌ Problem**: Login works but no email received
- **Fix**: Check email permission is requested in Facebook app settings

## Troubleshooting

### Common Issues

1. **🚫 OAuth buttons not appearing**
   - **Visual Check**: Login form should show OAuth buttons below the "Sign In" button
   - **Quick Fix**: Check that `OAUTH_ENABLED=true` in `.env`
   - **Debug**: Verify at least one provider is enabled (`OAUTH_GOOGLE_ENABLED=true`)
   - **Browser Check**: Open DevTools (F12) and look for JavaScript errors in Console tab

2. **🔄 OAuth callback errors**
   - **Visual Indicator**: Browser shows error page with "redirect_uri_mismatch" or similar
   - **URL Check**: Look at the address bar for error parameters
   - **Provider Fix**: Verify redirect URIs match exactly in provider settings
   - **Configuration**: Double-check client ID and secret in `.env` file
   - **Security**: Ensure state parameter validation is working (check session storage)

3. **👤 User creation failures**
   - **Database Check**: Verify database connection is working
   - **Model Check**: Confirm User model has OAuth fields (oauth_provider, oauth_id, etc.)
   - **Email Conflicts**: Check for existing users with same email address
   - **Visual Debug**: Look for error messages in browser console or network tab

4. **🔐 Authentication flow issues**
   - **Visual Cue**: User gets redirected but login doesn't complete
   - **Token Check**: Verify access tokens are being received and stored
   - **Session Check**: Confirm user session is being created properly
   - **Network Debug**: Check `/api/oauth/callback` request in DevTools Network tab

5. **🎨 Styling and UI issues**
   - **Button Appearance**: OAuth buttons should have provider colors and logos
   - **Mobile View**: Test on different screen sizes using DevTools device toolbar
   - **Loading States**: Buttons should show loading indicators during OAuth flow
   - **Error Messages**: User-friendly error messages should appear for failures

### Debug Mode

Enable debug logging by setting `APP_DEBUG=true` in your `.env` file.

**Visual Debug Checklist:**
- ✅ Check browser console for errors (F12 → Console)
- ✅ Monitor network requests (F12 → Network → filter by "oauth")
- ✅ Verify session storage contains OAuth state
- ✅ Test OAuth flow in incognito/private browsing mode
- ✅ Check provider dashboard for API usage and errors

## 📸 Screenshot Organization & Documentation Tips

> **💡 Pro Tip**: While this guide provides detailed visual landmarks, taking your own screenshots during setup can be invaluable for future reference and team onboarding.

### Recommended Screenshot Collection

**For Each Provider, Capture:**

1. **📋 Credentials Page**
   - Screenshot showing Client ID and Client Secret fields
   - Include the provider's logo/branding for easy identification
   - **File naming**: `oauth-{provider}-credentials.png`

2. **⚙️ Configuration Pages**
   - Redirect URI settings page
   - Scope/permissions configuration
   - **File naming**: `oauth-{provider}-config.png`

3. **✅ Success Confirmation**
   - Final setup confirmation or summary page
   - **File naming**: `oauth-{provider}-complete.png`

### Screenshot Organization Structure

```
_docs/
├── screenshots/
│   ├── oauth/
│   │   ├── google/
│   │   │   ├── 01-console-dashboard.png
│   │   │   ├── 02-create-credentials.png
│   │   │   ├── 03-oauth-client-form.png
│   │   │   ├── 04-redirect-uris.png
│   │   │   └── 05-client-credentials.png
│   │   ├── github/
│   │   │   ├── 01-developer-settings.png
│   │   │   ├── 02-oauth-apps.png
│   │   │   ├── 03-new-app-form.png
│   │   │   └── 04-app-credentials.png
│   │   └── facebook/
│   │       ├── 01-developers-dashboard.png
│   │       ├── 02-create-app.png
│   │       ├── 03-facebook-login-setup.png
│   │       └── 04-app-settings.png
│   └── troubleshooting/
│       ├── oauth-buttons-missing.png
│       ├── redirect-uri-error.png
│       └── console-errors.png
```

### Visual Documentation Best Practices

**📷 Screenshot Guidelines:**
- **Full Browser Window**: Capture entire browser window to show context
- **Highlight Important Areas**: Use red boxes or arrows to highlight key fields
- **Include URLs**: Show the address bar to confirm you're on the right page
- **Consistent Zoom Level**: Use 100% zoom for consistency across screenshots

**🏷️ Annotation Tips:**
- **Red Arrows**: Point to clickable buttons or links
- **Yellow Highlights**: Mark important text or values to copy
- **Green Checkmarks**: Indicate completed steps
- **Blue Boxes**: Frame important sections or forms

**📝 Documentation Updates:**
- **Version Control**: Keep screenshots updated when provider UIs change
- **Date Stamps**: Include capture date in filename or metadata
- **Team Sharing**: Store screenshots in shared location for team access
- **Mobile Screenshots**: Include mobile views for responsive testing

### Quick Reference Cards

**Create laminated quick-reference cards for each provider:**

**Google OAuth Card:**
```
🔗 URL: console.cloud.google.com
📍 Path: APIs & Services → Credentials
🎯 Look for: "CREATE CREDENTIALS" button
📋 Need: Client ID + Client Secret
⚠️ Remember: Enable Google+ API first
```

**GitHub OAuth Card:**
```
🔗 URL: github.com/settings/developers
📍 Path: Developer settings → OAuth Apps
🎯 Look for: "New OAuth App" button
📋 Need: Client ID + Client Secret
⚠️ Remember: Generate new secret each time
```

**Facebook OAuth Card:**
```
🔗 URL: developers.facebook.com
📍 Path: My Apps → Create App
🎯 Look for: "Facebook Login" product
📋 Need: App ID + App Secret
⚠️ Remember: Configure app domains
```

### Team Onboarding Checklist

**For new team members setting up OAuth:**

- [ ] **📖 Read this documentation** - Complete visual guide
- [ ] **🖥️ Access provider accounts** - Ensure team member has access
- [ ] **📸 Follow screenshot guide** - Take screenshots during setup
- [ ] **✅ Test OAuth flow** - Verify working integration
- [ ] **📝 Document any UI changes** - Update guide if provider UI changed
- [ ] **🔄 Share credentials securely** - Use team password manager
- [ ] **🧪 Test in development** - Verify localhost setup works
- [ ] **🚀 Test in production** - Verify production domains work

## Future Enhancements

- Support for additional OAuth providers (Twitter, LinkedIn, etc.)
- OAuth token refresh automation
- Advanced user profile synchronization
- OAuth scope management
- Multi-provider account linking
