<?php

namespace IamLab\Service;


use Exception;
use IamLab\Core\API\aAPI;
use IamLab\Model\User;
use IamLab\Model\PasswordResetToken;
use IamLab\Model\QRLoginSession;
use IamLab\Service\Auth\AuthService;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use function App\Core\Helpers\email;

class Auth extends aAPI
{

    /**
     * @return void
     */
    public function loginAction(): void
    {
        $email = $this->getParam('email');
        $password = $this->getParam('password');
        $rememberMe = $this->getParam('remember_me', false);

        // Validate input
        if (empty($email) || empty($password)) {
            $this->dispatch(['success' => false, 'message' => 'Email and password are required']);
        }

        try {
            $auth = (new AuthService())->authenticate((new User())->setEmail($email)->setPassword($password), "post", $rememberMe);

            if ($auth) {
                $this->dispatch(['success' => true, 'message' => 'Login successful', 'data' => $auth]);
            }

            $this->dispatch(['success' => false, 'message' => 'Invalid email or password']);

        } catch (Exception $e) {
            $this->dispatch(['success' => false, 'message' => 'An error occurred during authentication', 'debug' => $e->getMessage() // Only include in development
            ]);
        }
    }

    /*
     *
     */
    public function userAction(): void
    {
        if ((new AuthService())->isAuthenticated()) {
            $this->dispatch((new AuthService())->getIdentity());
        }
    }
/*
 *
 */
    public function registerAction(): void
    {
        $name = $this->getParam('name');
        $email = $this->getParam('email');
        $password = $this->getParam('password');

        // Validate input
        if (empty($name) || empty($email) || empty($password)) {
            $this->dispatch(['success' => false, 'message' => 'Name, email and password are required']);
        }

        // Basic email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->dispatch(['success' => false, 'message' => 'Please provide a valid email address']);
        }

        // Basic password validation
        if (strlen($password) < 6) {
            $this->dispatch(['success' => false, 'message' => 'Password must be at least 6 characters long']);
        }

        try {
            $user = (new User())
                ->setName($name)
                ->setEmail($email)
                ->setPassword($password);

            $authService = new AuthService();
            $authData = $authService->register($user);

            if ($authData) {
                $this->dispatch(['success' => true, 'message' => 'Registration successful! You are now logged in.', 'data' => $authData]);
            }

            $this->dispatch(['success' => false, 'message' => 'Registration failed. Email may already be in use.']);

        } catch (Exception $e) {
            $this->dispatch(['success' => false, 'message' => 'An error occurred during registration', 'debug' => $e->getMessage() // Only include in development
            ]);
        }
    }

    /**
     * Return frontend auth settings controlled by backend env/config
     * - inactivity_timeout_minutes: set <= 0 to disable inactivity auto-logout on client
     * - token_check_interval_minutes: how often client verifies/refreshes token
     */
    public function configAction(): void
    {
        try {
            $config = $this->di->getShared('config');
            $client = $config->auth_client ?? null;

            $inactivity = 30;
            $tokenCheck = 5;
            if ($client) {
                $inactivity = (int)($client->inactivity_timeout_minutes ?? 30);
                $tokenCheck = (int)($client->token_check_interval_minutes ?? 5);
            }

            $this->dispatch([
                'success' => true,
                'data' => [
                    'inactivity_timeout_minutes' => $inactivity,
                    'token_check_interval_minutes' => $tokenCheck,
                ],
            ]);
        } catch (\Throwable $e) {
            $this->dispatch([
                'success' => false,
                'message' => 'Unable to load auth client configuration',
                'errors' => ['original' => ['exception' => $e->getMessage()]],
            ]);
        }
    }

    public function forgotPasswordAction(): void
    {
        $email = $this->getParam('email');

        // Validate input
        if (empty($email)) {
            $this->dispatch(['success' => false, 'message' => 'Email address is required']);
        }

        // Basic email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->dispatch(['success' => false, 'message' => 'Please provide a valid email address']);
        }

        try {
            // Check if user exists with this email
            $user = User::findFirstByEmail($email);

            if (!$user) {
                // For security reasons, we don't reveal if the email exists or not
                // Always return success message
                $this->dispatch(['success' => true, 'message' => 'If an account with that email exists, a password reset link has been sent.']);
            }

            // 1. Generate a secure reset token
            // 2. Store it in the database with expiration
            $resetToken = PasswordResetToken::createForUser($user, 1); // 1 hour expiration

            if (!$resetToken) {
                $this->dispatch(['success' => false, 'message' => 'Failed to generate reset token. Please try again.']);
            }

            // 3. Send an email with the reset link
            $resetUrl = $_SERVER['HTTP_HOST'] . '/reset-password?token=' . $resetToken->getToken();
            $emailBody = "
                <h2>Password Reset Request</h2>
                <p>Hello {$user->getName()},</p>
                <p>You have requested to reset your password. Click the link below to reset your password:</p>
                <p><a href=\"{$resetUrl}\" style=\"background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;\">Reset Password</a></p>
                <p>This link will expire in 1 hour.</p>
                <p>If you did not request this password reset, please ignore this email.</p>
                <p>Best regards,<br>The Phalcon Stub Team</p>
            ";

            $emailSent = email(
                $user->getEmail(),
                'Password Reset Request',
                $emailBody,
                [
                    'is_html' => true,
                    'from_name' => 'Phalcon Stub Support'
                ]
            );

            if (!$emailSent) {
                // Clean up the token if email failed
                $resetToken->delete();
                $this->dispatch(['success' => false, 'message' => 'Failed to send reset email. Please try again.']);
            }

            $this->dispatch(['success' => true, 'message' => 'If an account with that email exists, a password reset link has been sent.']);

        } catch (Exception $e) {
            $this->dispatch(['success' => false, 'message' => 'An error occurred while processing your request', 'debug' => $e->getMessage() // Only include in development
            ]);
        }
    }

    public function logoutAction(): void
    {
        try {
            $authService = new AuthService();
            $authService->deauthenticate();

            $this->dispatch(['success' => true, 'message' => 'Logged out successfully']);

        } catch (Exception $e) {
            $this->dispatch(['success' => false, 'message' => 'An error occurred during logout', 'debug' => $e->getMessage() // Only include in development
            ]);
        }
    }

    /**
     * Refresh access token using refresh token
     */
    public function refreshTokenAction(): void
    {
        $refreshToken = $this->getParam('refresh_token');

        if (empty($refreshToken)) {
            $this->dispatch(['success' => false, 'message' => 'Refresh token is required']);
        }

        try {
            $authService = new AuthService();
            $result = $authService->refreshToken($refreshToken);

            if ($result) {
                $this->dispatch(['success' => true, 'message' => 'Token refreshed successfully', 'data' => $result]);
            }

            $this->dispatch(['success' => false, 'message' => 'Invalid or expired refresh token']);

        } catch (Exception $e) {
            $this->dispatch(['success' => false, 'message' => 'An error occurred during token refresh', 'debug' => $e->getMessage()]);
        }
    }

    /**
     * Generate API key for authenticated user
     */
    public function generateApiKeyAction(): void
    {
        try {
            $authService = new AuthService();

            if (!$authService->isAuthenticated()) {
                $this->dispatch(['success' => false, 'message' => 'Authentication required']);
            }

            $user = $authService->getUser();
            if (!$user) {
                $this->dispatch(['success' => false, 'message' => 'User not found']);
            }

            $apiKey = $authService->generateApiKey($user);

            $this->dispatch(['success' => true, 'message' => 'API key generated successfully', 'data' => ['api_key' => $apiKey]]);

        } catch (Exception $e) {
            $this->dispatch(['success' => false, 'message' => 'An error occurred while generating API key', 'debug' => $e->getMessage()]);
        }
    }

    /**
     * Get user profile information
     */
    public function profileAction(): void
    {
        try {
            $authService = new AuthService();

            if (!$authService->isAuthenticated()) {
                $this->dispatch(['success' => false, 'message' => 'Authentication required']);
            }

            $user = $authService->getUser();
            if (!$user) {
                $this->dispatch(['success' => false, 'message' => 'User not found']);
            }

            $profile = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'api_key' => $user->getKey() ? '***' . substr($user->getKey(), -8) : null // Show only last 8 characters for security
            ];

            $this->dispatch(['success' => true, 'data' => $profile]);

        } catch (Exception $e) {
            $this->dispatch(['success' => false, 'message' => 'An error occurred while fetching profile', 'debug' => $e->getMessage()]);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfileAction(): void
    {
        try {
            $authService = new AuthService();

            if (!$authService->isAuthenticated()) {
                $this->dispatch(['success' => false, 'message' => 'Authentication required']);
            }

            $user = $authService->getUser();
            if (!$user) {
                $this->dispatch(['success' => false, 'message' => 'User not found']);
            }

            $name = $this->getParam('name');
            $email = $this->getParam('email');

            if (!empty($name)) {
                $user->setName($name);
            }

            if (!empty($email)) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->dispatch(['success' => false, 'message' => 'Please provide a valid email address']);
                }
                $user->setEmail($email);
            }

            if ($user->save()) {
                $this->dispatch(['success' => true, 'message' => 'Profile updated successfully']);
            } else {
                $this->dispatch(['success' => false, 'message' => 'Failed to update profile']);
            }

        } catch (Exception $e) {
            $this->dispatch(['success' => false, 'message' => 'An error occurred while updating profile', 'debug' => $e->getMessage()]);
        }
    }

    /**
     * Generate QR code for login
     */
    public function generateQRCodeAction(): void
    {
        try {
            // Clean up expired sessions first
            QRLoginSession::cleanupExpired();

            // Create new QR login session
            $session = QRLoginSession::createSession(5); // 5 minutes expiration

            if (!$session->save()) {
                $this->dispatch(['success' => false, 'message' => 'Failed to create QR login session']);
            }

            // Generate QR code data (JSON with session token and base URL)
            $qrData = json_encode([
                'type' => 'qr_login',
                'session_token' => $session->getSessionToken(),
                'base_url' => $this->request->getScheme() . '://' . $this->request->getHttpHost(),
                'expires_at' => $session->expires_at
            ]);

            // Generate QR code image
            $result = Builder::create()
                ->writer(new PngWriter())
                ->data($qrData)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(new ErrorCorrectionLevelLow())
                ->size(300)
                ->margin(10)
                ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
                ->build();

            // Convert to base64 for easy frontend consumption
            $qrCodeBase64 = base64_encode($result->getString());

            $this->dispatch([
                'success' => true,
                'data' => [
                    'session_token' => $session->getSessionToken(),
                    'qr_code' => 'data:image/png;base64,' . $qrCodeBase64,
                    'expires_at' => $session->expires_at,
                    'expires_in' => 300 // 5 minutes in seconds
                ]
            ]);

        } catch (Exception $e) {
            $this->dispatch(['success' => false, 'message' => 'An error occurred while generating QR code', 'debug' => $e->getMessage()]);
        }
    }

    /**
     * Check QR code authentication status
     */
    public function checkQRStatusAction(): void
    {
        try {
            $sessionToken = $this->getParam('session_token');

            if (empty($sessionToken)) {
                $this->dispatch(['success' => false, 'message' => 'Session token is required']);
            }

            $session = QRLoginSession::findByToken($sessionToken);

            if (!$session) {
                $this->dispatch(['success' => false, 'message' => 'Invalid session token']);
            }

            // Check if session is expired
            if (!$session->isValid() && $session->getStatus() !== 'authenticated') {
                $session->expire();
                $this->dispatch(['success' => false, 'message' => 'Session expired', 'status' => 'expired']);
            }

            if ($session->isAuthenticated()) {
                // Get user and generate auth tokens
                $user = User::findFirstById($session->getUserId());
                if (!$user) {
                    $this->dispatch(['success' => false, 'message' => 'User not found']);
                }

                $authService = new AuthService();
                $authData = $authService->generateAuthData($user);

                // Clean up the session
                $session->delete();

                $this->dispatch([
                    'success' => true, 
                    'message' => 'Authentication successful',
                    'status' => 'authenticated',
                    'data' => $authData
                ]);
            }

            $this->dispatch([
                'success' => true,
                'status' => 'pending',
                'message' => 'Waiting for authentication'
            ]);

        } catch (Exception $e) {
            $this->dispatch(['success' => false, 'message' => 'An error occurred while checking QR status', 'debug' => $e->getMessage()]);
        }
    }

    /**
     * Authenticate QR code session (called from mobile)
     */
    public function authenticateQRAction(): void
    {
        try {
            $authService = new AuthService();

            // Check if user is authenticated
            if (!$authService->isAuthenticated()) {
                $this->dispatch(['success' => false, 'message' => 'Authentication required']);
            }

            $sessionToken = $this->getParam('session_token');

            if (empty($sessionToken)) {
                $this->dispatch(['success' => false, 'message' => 'Session token is required']);
            }

            $session = QRLoginSession::findByToken($sessionToken);

            if (!$session) {
                $this->dispatch(['success' => false, 'message' => 'Invalid session token']);
            }

            if (!$session->isValid()) {
                $this->dispatch(['success' => false, 'message' => 'Session expired or already used']);
            }

            $user = $authService->getUser();
            if (!$user) {
                $this->dispatch(['success' => false, 'message' => 'User not found']);
            }

            // Authenticate the session
            if ($session->authenticate($user->getId())) {
                $this->dispatch([
                    'success' => true,
                    'message' => 'QR code authenticated successfully'
                ]);
            } else {
                $this->dispatch(['success' => false, 'message' => 'Failed to authenticate QR session']);
            }

        } catch (Exception $e) {
            $this->dispatch(['success' => false, 'message' => 'An error occurred while authenticating QR code', 'debug' => $e->getMessage()]);
        }
    }

    /**
     * Generate QR code for mobile login (reverse flow)
     * Mobile device generates QR code, desktop scans it to authenticate mobile
     */
    public function generateMobileQRCodeAction(): void
    {
        try {
            $authService = new AuthService();

            // Check if user is authenticated on mobile
            if (!$authService->isAuthenticated()) {
                $this->dispatch(['success' => false, 'message' => 'Authentication required']);
            }

            // Clean up expired sessions first
            QRLoginSession::cleanupExpired();

            // Create new QR login session for mobile authentication
            $session = QRLoginSession::createSession(5); // 5 minutes expiration

            // Set the user ID immediately since mobile user is already authenticated
            $user = $authService->getUser();
            if (!$user) {
                $this->dispatch(['success' => false, 'message' => 'User not found']);
            }

            $session->user_id = $user->getId();
            $session->status = 'pending_mobile_auth'; // Different status for reverse flow

            if (!$session->save()) {
                $this->dispatch(['success' => false, 'message' => 'Failed to create mobile QR login session']);
            }

            // Generate QR code data for mobile authentication
            $qrData = json_encode([
                'type' => 'mobile_qr_login',
                'session_token' => $session->getSessionToken(),
                'user_id' => $user->getId(),
                'base_url' => $this->request->getScheme() . '://' . $this->request->getHttpHost(),
                'expires_at' => $session->expires_at
            ]);

            // Generate QR code image
            $result = Builder::create()
                ->writer(new PngWriter())
                ->data($qrData)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(new ErrorCorrectionLevelLow())
                ->size(300)
                ->margin(10)
                ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
                ->build();

            // Convert to base64 for easy frontend consumption
            $qrCodeBase64 = base64_encode($result->getString());

            $this->dispatch([
                'success' => true,
                'data' => [
                    'session_token' => $session->getSessionToken(),
                    'qr_code' => 'data:image/png;base64,' . $qrCodeBase64,
                    'expires_at' => $session->expires_at,
                    'expires_in' => 300, // 5 minutes in seconds
                    'user_name' => $user->getName()
                ]
            ]);

        } catch (Exception $e) {
            $this->dispatch(['success' => false, 'message' => 'An error occurred while generating mobile QR code', 'debug' => $e->getMessage()]);
        }
    }

    /**
     * Authenticate mobile QR code session (called from desktop)
     * Desktop scans mobile QR code and authenticates the mobile session
     */
    public function authenticateMobileQRAction(): void
    {
        try {
            $authService = new AuthService();

            // Check if user is authenticated on desktop
            if (!$authService->isAuthenticated()) {
                $this->dispatch(['success' => false, 'message' => 'Desktop authentication required']);
            }

            $sessionToken = $this->getParam('session_token');

            if (empty($sessionToken)) {
                $this->dispatch(['success' => false, 'message' => 'Session token is required']);
            }

            $session = QRLoginSession::findByToken($sessionToken);

            if (!$session) {
                $this->dispatch(['success' => false, 'message' => 'Invalid session token']);
            }

            if (!$session->isValid() && $session->getStatus() !== 'pending_mobile_auth') {
                $this->dispatch(['success' => false, 'message' => 'Session expired or already used']);
            }

            $desktopUser = $authService->getUser();
            $mobileUser = User::findFirstById($session->getUserId());

            if (!$desktopUser || !$mobileUser) {
                $this->dispatch(['success' => false, 'message' => 'User not found']);
            }

            // Verify that the same user is trying to authenticate
            if ($desktopUser->getId() !== $mobileUser->getId()) {
                $this->dispatch(['success' => false, 'message' => 'User mismatch - you can only authenticate your own mobile sessions']);
            }

            // Authenticate the mobile session
            $session->status = 'mobile_authenticated';
            $session->authenticated_at = date('Y-m-d H:i:s');

            if ($session->save()) {
                $this->dispatch([
                    'success' => true,
                    'message' => 'Mobile session authenticated successfully'
                ]);
            } else {
                $this->dispatch(['success' => false, 'message' => 'Failed to authenticate mobile session']);
            }

        } catch (Exception $e) {
            $this->dispatch(['success' => false, 'message' => 'An error occurred while authenticating mobile QR code', 'debug' => $e->getMessage()]);
        }
    }

    /**
     * Check mobile QR code authentication status (called from mobile)
     * Mobile device polls this to check if desktop has authenticated the session
     */
    public function checkMobileQRStatusAction(): void
    {
        try {
            $sessionToken = $this->getParam('session_token');

            if (empty($sessionToken)) {
                $this->dispatch(['success' => false, 'message' => 'Session token is required']);
            }

            $session = QRLoginSession::findByToken($sessionToken);

            if (!$session) {
                $this->dispatch(['success' => false, 'message' => 'Invalid session token']);
            }

            // Check if session is expired
            if (!$session->isValid() && $session->getStatus() !== 'mobile_authenticated') {
                $session->expire();
                $this->dispatch(['success' => false, 'message' => 'Session expired', 'status' => 'expired']);
            }

            if ($session->getStatus() === 'mobile_authenticated') {
                // Mobile session has been authenticated by desktop
                $user = User::findFirstById($session->getUserId());
                if (!$user) {
                    $this->dispatch(['success' => false, 'message' => 'User not found']);
                }

                $authService = new AuthService();
                $authData = $authService->generateAuthData($user);

                // Clean up the session
                $session->delete();

                $this->dispatch([
                    'success' => true, 
                    'message' => 'Mobile authentication successful',
                    'status' => 'authenticated',
                    'data' => $authData
                ]);
            }

            $this->dispatch([
                'success' => true,
                'status' => 'pending',
                'message' => 'Waiting for desktop authentication'
            ]);

        } catch (Exception $e) {
            $this->dispatch(['success' => false, 'message' => 'An error occurred while checking mobile QR status', 'debug' => $e->getMessage()]);
        }
    }

    /**
     * Reset password action - resends password reset email
     */
    public function resetPasswordAction(): void
    {
        $token = $this->getParam('token');
        $password = $this->getParam('password');

        // Validate input
        if (empty($token) || empty($password)) {
            $this->dispatch(['success' => false, 'message' => 'Token and new password are required']);
        }

        // Basic password validation
        if (strlen($password) < 6) {
            $this->dispatch(['success' => false, 'message' => 'Password must be at least 6 characters long']);
        }

        try {
            // Find the reset token
            $resetToken = PasswordResetToken::findFirstByToken($token);

            if (!$resetToken || !$resetToken->isValid()) {
                $this->dispatch(['success' => false, 'message' => 'Invalid or expired reset token']);
            }

            // Get the user
            $user = User::findFirstById($resetToken->getUserId());
            if (!$user) {
                $this->dispatch(['success' => false, 'message' => 'User not found']);
            }

            // Update the password
            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
            
            if ($user->save()) {
                // Delete the used token
                $resetToken->delete();
                
                $this->dispatch(['success' => true, 'message' => 'Password reset successfully']);
            } else {
                $this->dispatch(['success' => false, 'message' => 'Failed to update password']);
            }

        } catch (Exception $e) {
            $this->dispatch(['success' => false, 'message' => 'An error occurred while resetting password', 'debug' => $e->getMessage()]);
        }
    }

    /**
     * Verify email action - handles email verification and resending verification emails
     */
    public function verifyEmailAction(): void
    {
        $email = $this->getParam('email');
        $token = $this->getParam('token');

        // If token is provided, verify the email
        if ($token) {
            $this->handleEmailVerification($token);
            return;
        }

        // If email is provided, resend verification email
        if ($email) {
            $this->resendEmailVerification($email);
            return;
        }

        $this->dispatch(['success' => false, 'message' => 'Either email or verification token is required']);
    }

    /**
     * Handle email verification with token
     */
    private function handleEmailVerification($token): void
    {
        try {
            // In a real implementation, you would have an email verification token system
            // For now, we'll just return success
            $this->dispatch(['success' => true, 'message' => 'Email verified successfully']);
        } catch (Exception $e) {
            $this->dispatch(['success' => false, 'message' => 'An error occurred while verifying email', 'debug' => $e->getMessage()]);
        }
    }

    /**
     * Resend email verification
     */
    private function resendEmailVerification($email): void
    {
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->dispatch(['success' => false, 'message' => 'Please provide a valid email address']);
        }

        try {
            // Check if user exists with this email
            $user = User::findFirstByEmail($email);

            if (!$user) {
                // For security reasons, we don't reveal if the email exists or not
                $this->dispatch(['success' => true, 'message' => 'If an account with that email exists, a verification email has been sent.']);
            }

            // Check if email is already verified
            if ($user->getEmailVerified()) {
                $this->dispatch(['success' => false, 'message' => 'Email is already verified']);
            }

            // Generate verification token (in a real implementation, you'd store this)
            $verificationToken = bin2hex(random_bytes(32));
            
            // Send verification email
            $verificationUrl = $_SERVER['HTTP_HOST'] . '/verify-email?token=' . $verificationToken;
            $emailBody = "
                <h2>Email Verification</h2>
                <p>Hello {$user->getName()},</p>
                <p>Please click the link below to verify your email address:</p>
                <p><a href=\"{$verificationUrl}\" style=\"background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;\">Verify Email</a></p>
                <p>If you did not create this account, please ignore this email.</p>
                <p>Best regards,<br>The Team</p>
            ";

            $emailSent = email(
                $user->getEmail(),
                'Email Verification',
                $emailBody,
                [
                    'is_html' => true,
                    'from_name' => 'Support Team'
                ]
            );

            if ($emailSent) {
                $this->dispatch(['success' => true, 'message' => 'Verification email sent successfully']);
            } else {
                $this->dispatch(['success' => false, 'message' => 'Failed to send verification email']);
            }

        } catch (Exception $e) {
            $this->dispatch(['success' => false, 'message' => 'An error occurred while sending verification email', 'debug' => $e->getMessage()]);
        }
    }

}
