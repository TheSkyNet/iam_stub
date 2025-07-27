<?php

/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 22/10/2015
 * Time: 19:05
 */

namespace IamLab\Service\Auth;

use Exception;
use IamLab\Core\API\aAPI;
use IamLab\Model\User;

/**
 * Class AuthService
 * Handles user authentication, registration, and session management.
 * Integrates JWT for stateless API authentication and falls back to session-based auth.
 *
 * @package IamLab\Service\Auth
 */
class AuthService extends aAPI
{
    /**
     * @var bool Tracks the current authentication status.
     */
    public bool $isAuthenticated;

    /**
     * @var mixed Stores the state or identity of the authenticated user.
     */
    public mixed $state;

    /**
     * @var JwtService|null Holds the instance of the JWT service.
     */
    private ?JwtService $jwtService = null;

    /**
     * Checks if a user is currently authenticated.
     *
     * @return bool True if authenticated, false otherwise.
     */
    public function isAuthenticated(): bool
    {
        return (bool)$this->getIdentity();
    }

    /**
     * Retrieves the identity of the current user.
     * It first attempts to validate a JWT from the Authorization header,
     * then falls back to checking the session for backward compatibility.
     *
     * @return array|null The user's identity data or null if not authenticated.
     */
    public function getIdentity(): ?array
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
     * Lazily initializes and returns an instance of the JwtService.
     *
     * @return JwtService The JWT service instance.
     */
    private function getJwtService(): JwtService
    {
        if ($this->jwtService === null) {
            $this->jwtService = new JwtService();
        }
        return $this->jwtService;
    }

    /**
     * Retrieves the full User model for the currently authenticated user.
     *
     * @return User|null The User object or null if not found or not authenticated.
     */
    public function getUser(): ?User
    {
        $identity = $this->getIdentity();
        if (!$identity) {
            return null;
        }
        return User::findFirstById($identity['user_id']);
    }

    /**
     * Authenticates a user based on the provided method.
     *
     * @param User $user The user object with credentials.
     * @param string $authMethod The authentication strategy to use (e.g., "post").
     * @param bool $rememberMe Whether to extend token expiration for "Remember me" functionality.
     * @return array|false Auth data on success, false on failure.
     */
    public function authenticate(User $user, string $authMethod = "post", bool $rememberMe = false): bool|array
    {
        if ($authMethod == "post") {
            return $this->authenticatePost($user, $rememberMe);
        }

        return false;
    }

    /**
     * Handles standard email/password authentication.
     * Verifies credentials and generates JWT tokens upon success.
     *
     * @param User $user The user attempting to log in.
     * @param bool $rememberMe Whether to extend token expiration for "Remember me" functionality.
     * @return array|false An array with tokens and user data on success, false otherwise.
     */
    private function authenticatePost(User $user, bool $rememberMe = false): bool|array
    {
        $password = $user->getPassword();
        $user = User::findFirst("email='{$user->getEmail()}'");

        if (!$user) {
            return false;
        }

        if (password_verify($password, $user->getPassword())) {
            // Generate JWT tokens with extended expiration if "Remember me" is checked
            $accessToken = $this->getJwtService()->generateAccessToken($user, $rememberMe);
            $refreshToken = $this->getJwtService()->generateRefreshToken($user, $rememberMe);

            // Set identity for session compatibility (optional)
            $this->setIdentity($user);

            // Set appropriate expires_in based on remember me option
            $expiresIn = $rememberMe ? (30 * 24 * 3600) : 3600; // 30 days or 1 hour

            // Create user data with roles
            $userData = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles()
            ];

            return [
                'user' => $userData, 
                'access_token' => $accessToken, 
                'refresh_token' => $refreshToken, 
                'expires_in' => $expiresIn,
                'token_type' => 'Bearer'
            ];
        }
        return false;
    }

    /**
     * Stores the user's identity in the session.
     *
     * @param User $user The user to identify in the session.
     */
    private function setIdentity(User $user): void
    {
        // Get user roles
        $roles = $user->getRoles();
        
        $this->session->set('auth-identity', [
            'id' => $user->id, 
            'name' => $user->name, 
            'email' => $user->email,
            'roles' => $roles
        ]);
    }

    /**
     * Registers a new user.
     * Hashes the password, validates the data, and creates the user record.
     * On success, it generates and returns authentication tokens.
     *
     * @param User $user The new user data.
     * @return array|false Returns authentication data array on success, false on failure.
     */
    public function register(User $user): bool|array
    {
        $user->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));

        if (!$user->validation()) {
            return false;
        }

        $userFind = User::findFirstByEmail($user->getEmail());

        if (!empty($userFind->id)) {
            return false;
        }

        if (!$user->save()) {
            dump($user->getMessages());
            return false;

        }

        // Generate JWT tokens for the newly registered user
        $accessToken = $this->getJwtService()->generateAccessToken($user);
        $refreshToken = $this->getJwtService()->generateRefreshToken($user);

        // Set identity for session compatibility
        $this->setIdentity($user);

        // Create user data with roles
        $userData = [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles()
        ];

        // Return the same authentication data structure as login
        return [
            'user' => $userData, 
            'access_token' => $accessToken, 
            'refresh_token' => $refreshToken, 
            'expires_in' => 3600, // 1 hour
            'token_type' => 'Bearer'
        ];
    }

    /**
     * Deauthenticates the current user by destroying their session and clearing identity.
     *
     * @return bool Returns true if deauthentication was successful.
     * @throws Exception If session destruction fails.
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
     * Refreshes an expired access token using a valid refresh token.
     *
     * @param string $refreshToken The refresh token.
     * @return array|null A new set of tokens on success, null on failure.
     */
    public function refreshToken(string $refreshToken): ?array
    {
        return $this->getJwtService()->refreshAccessToken($refreshToken);
    }

    /**
     * Generates a new API key for a given user and saves it to the database.
     *
     * @param User $user The user for whom to generate the key.
     * @return string The newly generated API key.
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
     * Validates an API key and returns the associated user if valid.
     *
     * @param string $apiKey The API key to validate.
     * @return User|null The user associated with the key, or null if invalid.
     */
    public function validateApiKey(string $apiKey): ?User
    {
        return $this->getJwtService()->validateApiKey($apiKey);
    }

    /**
     * Retrieves a user from a JWT token payload.
     *
     * @param string $token The JWT access token.
     * @return User|null The user object or null if the token is invalid.
     */
    public function getUserFromToken(string $token): ?User
    {
        return $this->getJwtService()->getUserFromToken($token);
    }

    /**
     * Generates a full authentication data payload for a user.
     * Used for login methods like QR code scanning where credentials aren't posted.
     *
     * @param User $user The user to generate auth data for.
     * @return array The authentication data payload.
     */
    public function generateAuthData(User $user , $options = []): array
    {
        $defaultOptions =[
            'expires_in' => 3600, // 1 hour
            'token_type' => 'Bearer'
        ];
        $options = array_merge($defaultOptions, $options);
        // Generate JWT tokens
        $accessToken = $this->getJwtService()->generateAccessToken($user);
        $refreshToken = $this->getJwtService()->generateRefreshToken($user);

        // Set identity for session compatibility
        $this->setIdentity($user);

        // Create user data with roles
        $userData = [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles()
        ];

        return [
            'user' => $userData,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => $options['expires_in'],
            'token_type' => $options['token_type']
        ];

    }

}
