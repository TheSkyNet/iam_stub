<?php

/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 22/10/2015
 * Time: 19:05
 */

namespace IamLab\Service\Auth;

use IamLab\Core\API\aAPI;
use IamLab\Model\User;
use IamLab\Service\Auth\JwtService;
use Phalcon\Mvc\User\Component;
use Exception;
use function App\Core\Helpers\dd;

class AuthService extends aAPI
{
    public $isAuthenticated;
    public $state;
    private ?JwtService $jwtService = null;

    public function __construct()
    {
        // No need to call parent constructor as aAPI doesn't have one
    }

    /**
     * Get or create JwtService instance
     */
    private function getJwtService(): JwtService
    {
        if ($this->jwtService === null) {
            $this->jwtService = new JwtService();
        }
        return $this->jwtService;
    }

    public function isAuthenticated()
    {
        return (bool)$this->getIdentity();
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        $identity = $this->getIdentity();
        if (!$identity) {
            return null;
        }
        return User::findFirstById($identity['user_id']);
    }

    public function authenticate(User $user, $authMethod = "post")
    {
        if ($authMethod == "post") {
            return $this->authenticatePost($user);
        }

        return false;
    }

    private function authenticatePost(User $user)
    {
        $password = $user->getPassword();
        $user = User::findFirst("email='{$user->getEmail()}'");

        if (!$user) {
            return false;
        }

        if (password_verify($password, $user->getPassword())) {
            // Generate JWT tokens
            $accessToken = $this->getJwtService()->generateAccessToken($user);
            $refreshToken = $this->getJwtService()->generateRefreshToken($user);

            // Set identity for session compatibility (optional)
            $this->setIdentity($user);

            return [
                'user' => $user,
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'expires_in' => 3600, // 1 hour
                'token_type' => 'Bearer'
            ];
        }
        return false;
    }

    private function setIdentity($user)
    {

        $this->session->set('auth-identity', ['id' => $user->id, 'name' => $user->name, 'email' => $user->email,]);
    }

    public function getIdentity()
    {
        // First try to get identity from JWT token
        $token = $this->getJwtService()->extractTokenFromHeader();
        if ($token) {
            try {
                $payload = $this->getJwtService()->validateToken($token);
                if ($payload['type'] === 'access') {
                    return $payload;
                }
            } catch (Exception $e) {
                // Token is invalid, fall back to session
            }
        }

        // Fall back to session-based identity for backward compatibility
        return $this->session->get('auth-identity');
    }


    /**
     * @param User $user
     *
     * @return array|false Returns authentication data array on success, false on failure
     */
    public function register(User $user)
    {
        $user->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));

        if (!$user->validation()) {
            return false;
        }

        $userFind = User::findFirstByEmail($user->getEmail());

        if (!empty($userFind->id)) {
            return false;
        }

        if ($user->save() == false) {
            dump($user->getMessages());
            return false;

        }

        // Generate JWT tokens for the newly registered user
        $accessToken = $this->getJwtService()->generateAccessToken($user);
        $refreshToken = $this->getJwtService()->generateRefreshToken($user);

        // Set identity for session compatibility
        $this->setIdentity($user);

        // Return the same authentication data structure as login
        return [
            'user' => $user,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => 3600, // 1 hour
            'token_type' => 'Bearer'
        ];
    }
    /**
     * Deauthenticates the current user by destroying their session and clearing identity
     *
     * @return bool Returns true if deauthentication was successful
     * @throws Exception If session destruction fails
     */
    public function deauthenticate(): bool
    {
        try {
            // Clear the auth identity first
            $this->session->remove('auth-identity');

            // Reset session state
            $this->isAuthenticated = false;
            $this->state = null;

            // Regenerate the session ID to prevent session fixation attacks
            $this->session->regenerateId(true);

            // Destroy the session
            $this->session->destroy();

            return true;

        } catch (Exception $e) {
            // Log the error if you have a logger configured
            if (isset($this->logger)) {
                $this->logger->error('Deauthentication failed: ' . $e->getMessage());
            }
            throw $e;
        }
    }

    /**
     * Refresh access token using refresh token
     */
    public function refreshToken(string $refreshToken): ?array
    {
        return $this->getJwtService()->refreshAccessToken($refreshToken);
    }

    /**
     * Generate API key for user
     */
    public function generateApiKey(User $user): string
    {
        $apiKey = $this->getJwtService()->generateApiKey($user);

        // Update user's key field in database
        $user->setKey($apiKey);
        $user->save();

        return $apiKey;
    }

    /**
     * Validate API key and return user
     */
    public function validateApiKey(string $apiKey): ?User
    {
        return $this->getJwtService()->validateApiKey($apiKey);
    }

    /**
     * Get user from JWT token
     */
    public function getUserFromToken(string $token): ?User
    {
        return $this->getJwtService()->getUserFromToken($token);
    }

    /**
     * Generate authentication data for a user (for QR login)
     */
    public function generateAuthData(User $user): array
    {
        // Generate JWT tokens
        $accessToken = $this->getJwtService()->generateAccessToken($user);
        $refreshToken = $this->getJwtService()->generateRefreshToken($user);

        // Set identity for session compatibility
        $this->setIdentity($user);

        return [
            'user' => $user,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => 3600, // 1 hour
            'token_type' => 'Bearer'
        ];
    }

}
