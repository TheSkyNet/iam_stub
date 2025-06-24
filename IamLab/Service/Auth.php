<?php

namespace IamLab\Service;


use Exception;
use IamLab\Core\API\aAPI;
use IamLab\Model\User;
use IamLab\Service\Auth\AuthService;

class Auth extends aAPI
{

    /**
     * @return void
     */
    public function authAction(): void
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

            $this->dispatch(['success' => true, 'message' => 'If an account with that email exists, a password reset link has been sent.']);
            if (!$user) {
                // For security reasons, we don't reveal if the email exists or not
                // Always return success message
                return;
            }

            // In a real application, you would:
            // 1. Generate a secure reset token
            // 2. Store it in the database with expiration
            // 3. Send an email with the reset link
            // 
            // For this stub project, we'll just return a success message

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

}
