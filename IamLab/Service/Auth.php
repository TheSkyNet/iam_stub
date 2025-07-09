<?php

namespace IamLab\Service;


use Exception;
use IamLab\Core\API\aAPI;
use IamLab\Model\User;
use IamLab\Model\PasswordResetToken;
use IamLab\Service\Auth\AuthService;
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

        // Validate input
        if (empty($email) || empty($password)) {
            $this->dispatch(['success' => false, 'message' => 'Email and password are required']);
            return;
        }

        try {
            $auth = (new AuthService())->authenticate((new User())->setEmail($email)->setPassword($password));

            if ($auth) {
                $this->dispatch(['success' => true, 'message' => 'Login successful', 'data' => $auth]);
                return;
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
            return;
        }

        // Basic email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->dispatch(['success' => false, 'message' => 'Please provide a valid email address']);
            return;
        }

        // Basic password validation
        if (strlen($password) < 6) {
            $this->dispatch(['success' => false, 'message' => 'Password must be at least 6 characters long']);
            return;
        }

        try {
            $user = (new User())
                ->setName($name)
                ->setEmail($email)
                ->setPassword($password);

            $authService = new AuthService();
            $result = $authService->register($user);

            if ($result) {
                $this->dispatch(['success' => true, 'message' => 'Registration successful! You are now logged in.', 'data' => $authService->getIdentity()]);
                return;
            }

            $this->dispatch(['success' => false, 'message' => 'Registration failed. Email may already be in use.']);

        } catch (Exception $e) {
            $this->dispatch(['success' => false, 'message' => 'An error occurred during registration', 'debug' => $e->getMessage() // Only include in development
            ]);
        }
    }

    public function forgotPasswordAction(): void
    {
        $email = $this->getParam('email');

        // Validate input
        if (empty($email)) {
            $this->dispatch(['success' => false, 'message' => 'Email address is required']);
            return;
        }

        // Basic email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->dispatch(['success' => false, 'message' => 'Please provide a valid email address']);
            return;
        }

        try {
            // Check if user exists with this email
            $user = User::findFirstByEmail($email);

            if (!$user) {
                // For security reasons, we don't reveal if the email exists or not
                // Always return success message
                $this->dispatch(['success' => true, 'message' => 'If an account with that email exists, a password reset link has been sent.']);
                return;
            }

            // 1. Generate a secure reset token
            // 2. Store it in the database with expiration
            $resetToken = PasswordResetToken::createForUser($user, 1); // 1 hour expiration

            if (!$resetToken) {
                $this->dispatch(['success' => false, 'message' => 'Failed to generate reset token. Please try again.']);
                return;
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
                return;
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
            return;
        }

        try {
            $authService = new AuthService();
            $result = $authService->refreshToken($refreshToken);

            if ($result) {
                $this->dispatch(['success' => true, 'message' => 'Token refreshed successfully', 'data' => $result]);
                return;
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
                return;
            }

            $user = $authService->getUser();
            if (!$user) {
                $this->dispatch(['success' => false, 'message' => 'User not found']);
                return;
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
                return;
            }

            $user = $authService->getUser();
            if (!$user) {
                $this->dispatch(['success' => false, 'message' => 'User not found']);
                return;
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
                return;
            }

            $user = $authService->getUser();
            if (!$user) {
                $this->dispatch(['success' => false, 'message' => 'User not found']);
                return;
            }

            $name = $this->getParam('name');
            $email = $this->getParam('email');

            if (!empty($name)) {
                $user->setName($name);
            }

            if (!empty($email)) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->dispatch(['success' => false, 'message' => 'Please provide a valid email address']);
                    return;
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

}
