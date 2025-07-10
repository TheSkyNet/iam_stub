# Mobile Login Feature Documentation

## Overview

The Mobile Login feature allows users to authenticate on desktop computers using their mobile device. This feature is integrated into the user profile page and works in conjunction with the existing QR code login system.

## How It Works

### For Users

1. **Desktop Side**: User goes to the login page and clicks the "QR Code" tab to generate a QR code
2. **Mobile Side**: User opens their profile page on their mobile device and uses the "Mobile Login" section
3. **Authentication**: User scans the QR code from desktop and clicks "Authenticate Desktop" to complete the login

### Technical Flow

1. Desktop generates QR code with session token via `/auth/generate-qr-code`
2. Mobile device scans QR code to extract session token
3. Mobile device calls `/auth/authenticate-qr` with the session token
4. Desktop polls `/auth/check-qr-status` and receives authentication confirmation
5. Desktop completes login with received JWT tokens

## Features

### Profile Page Integration

- **Location**: Available in the user profile page under "Mobile Login" section
- **Access**: Users must be logged in to access the mobile login scanner
- **UI**: Clean, intuitive interface with step-by-step instructions

### QR Scanner Interface

- **Start Scanner**: Button to activate the QR scanning mode
- **Manual Input**: Fallback textarea for manual QR data input (useful for testing)
- **Token Detection**: Automatic parsing of QR code JSON data
- **Authentication**: One-click authentication of desktop sessions

### User Experience

- **Visual Feedback**: Clear indicators for scanner state and token detection
- **Error Handling**: Comprehensive error messages and user guidance
- **Instructions**: Built-in help text explaining the process
- **Responsive Design**: Works well on both mobile and desktop devices

## API Endpoints Used

### Generate QR Code (Desktop)
```
POST /auth/generate-qr-code
Response: {
  "success": true,
  "data": {
    "session_token": "...",
    "qr_code": "data:image/png;base64,...",
    "expires_at": "2024-01-01 12:05:00",
    "expires_in": 300
  }
}
```

### Authenticate QR Session (Mobile)
```
POST /auth/authenticate-qr
Headers: Authorization: Bearer <access_token>
Body: { "session_token": "..." }
Response: {
  "success": true,
  "message": "QR code authenticated successfully"
}
```

### Check QR Status (Desktop)
```
POST /auth/check-qr-status
Body: { "session_token": "..." }
Response: {
  "success": true,
  "status": "authenticated",
  "data": {
    "user": {...},
    "access_token": "...",
    "refresh_token": "...",
    "expires_in": 3600,
    "token_type": "Bearer"
  }
}
```

## Security Features

- **Session Expiration**: QR codes expire after 5 minutes
- **One-time Use**: Each QR session can only be used once
- **Authentication Required**: Mobile user must be logged in to authenticate QR codes
- **Token Validation**: Server validates session tokens and user permissions
- **Automatic Cleanup**: Expired sessions are automatically cleaned up

## User Interface Components

### Profile Page Structure
```
User Profile
├── Personal Information
├── API Key Management
├── Mobile Login ← NEW FEATURE
│   ├── QR Scanner Interface
│   ├── Manual Input (fallback)
│   ├── Authentication Controls
│   └── Usage Instructions
└── Account Actions
```

### Mobile Login Section Features
- **Scanner State Management**: Toggle between inactive and active states
- **Token Processing**: Parse and validate QR code data
- **Authentication Flow**: Handle desktop authentication requests
- **User Feedback**: Success/error messages and loading states

## Testing

### Manual Testing
1. Open `test_mobile_login.html` in a browser
2. Click "Start Scanner" to activate the scanner
3. Paste test QR data in the textarea:
   ```json
   {"type":"qr_login","session_token":"test_session_token_12345678901234567890","base_url":"http://localhost:8080","expires_at":"2024-01-01 12:00:00"}
   ```
4. Click "Authenticate Desktop" to test the authentication flow

### Integration Testing
1. Start the Phalcon application
2. Navigate to `/profile` while logged in
3. Test the mobile login functionality with real QR codes from `/login`

## File Changes

### Modified Files
- `assets/js/components/Profile.js` - Added mobile login section
- `assets/js/services/AuthserviceService.js` - QR authentication methods (already existed)
- `IamLab/Service/Auth.php` - QR authentication endpoints (already existed)

### New Files
- `test_mobile_login.html` - Standalone test page for mobile login feature
- `MOBILE_LOGIN_FEATURE.md` - This documentation file

## Usage Instructions

### For End Users

1. **Setup**: Ensure you're logged into your account on your mobile device
2. **Access**: Go to your profile page and find the "Mobile Login" section
3. **Scan**: Click "Start Scanner" when you want to authenticate a desktop login
4. **Authenticate**: Scan the QR code from the desktop login page and click "Authenticate Desktop"

### For Developers

1. **Integration**: The feature is automatically available in the profile page
2. **Customization**: Modify `Profile.js` to customize the UI or behavior
3. **Extension**: Add camera integration for real QR code scanning (currently uses manual input)

## Future Enhancements

- **Camera Integration**: Add real camera-based QR code scanning
- **Push Notifications**: Notify users of authentication requests
- **Session Management**: Show active QR sessions in the profile
- **Biometric Authentication**: Add fingerprint/face ID confirmation
- **Multi-device Support**: Allow multiple mobile devices per account

## Troubleshooting

### Common Issues

1. **Scanner Not Working**: Ensure JavaScript is enabled and the page is served over HTTPS for camera access
2. **Authentication Fails**: Check that the user is logged in and the session token is valid
3. **QR Code Expired**: Generate a new QR code on the desktop login page
4. **Network Issues**: Ensure both devices have internet connectivity

### Error Messages

- "Authentication required" - User needs to log in on mobile device
- "Invalid session token" - QR code data is malformed or expired
- "Session expired or already used" - QR code has expired or been used
- "Failed to authenticate QR session" - Server error during authentication

## Conclusion

The Mobile Login feature provides a secure and convenient way for users to authenticate on desktop computers using their mobile devices. It integrates seamlessly with the existing authentication system and provides a modern, user-friendly experience.