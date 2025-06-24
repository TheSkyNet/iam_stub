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
        return;
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        (new AuthService())->register((new User())->setName($email)->setEmail($email)->setPassword($password));
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