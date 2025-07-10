<?php

namespace IamLab\Service\Auth;

use IamLab\Core\API\aAPI;
use IamLab\Model\User;
use Exception;
use function App\Core\Helpers\config;

abstract class OAuthService extends aAPI
{
    protected string $provider;
    protected array $config;

    /**
     * @throws Exception
     */
    public function __construct(string $provider)
    {
        $this->provider = $provider;
        $this->config = config('oauth.' . $provider);

        if (!$this->isEnabled()) {
            throw new Exception("OAuth provider '{$provider}' is not enabled");
        }
    }

    /**
     * Check if the OAuth provider is enabled
     */
    public function isEnabled(): bool
    {
        return !empty($this->config['enabled']) && $this->config['enabled'];
    }

    /**
     * Get the authorization URL for the OAuth provider
     */
    abstract public function getAuthorizationUrl(string $state = null): string;

    /**
     * Exchange authorization code for access token
     */
    abstract public function getAccessToken(string $code, string $state = null): array;

    /**
     * Get user information from the OAuth provider
     */
    abstract public function getUserInfo(string $accessToken): array;

    /**
     * Create or update user from OAuth data
     * @throws Exception
     */
    public function createOrUpdateUser(array $oauthUser): User
    {
        // Check if user exists by email
        $user = User::findFirst([
            'conditions' => 'email = :email:',
            'bind' => ['email' => $oauthUser['email']]
        ]);

        if (!$user) {
            // Create new user
            $user = new User();
            $user->email = $oauthUser['email'];
            $user->name = $oauthUser['name'] ?? '';
            $user->avatar = $oauthUser['avatar'] ?? '';
            $user->password = null; // OAuth users don't have passwords
            $user->oauth_provider = $this->provider;
            $user->oauth_id = $oauthUser['id'];
            $user->email_verified = true; // OAuth providers typically verify emails
            $user->created_at = date('Y-m-d H:i:s');
            $user->updated_at = date('Y-m-d H:i:s');

            if (!$user->save()) {
                throw new Exception('Failed to create user: ' . implode(', ', $user->getMessages()));
            }
        } else {
            // Update existing user with OAuth info if not already set
            if (empty($user->oauth_provider)) {
                $user->oauth_provider = $this->provider;
                $user->oauth_id = $oauthUser['id'];
            }

            // Update avatar if provided and current is empty
            if (!empty($oauthUser['avatar']) && empty($user->avatar)) {
                $user->avatar = $oauthUser['avatar'];
            }

            $user->updated_at = date('Y-m-d H:i:s');
            $user->save();
        }

        return $user;
    }

    /**
     * Generate a random state parameter for OAuth security
     */
    protected function generateState(): string
    {
        return bin2hex(random_bytes(16));
    }


    /**
     * Build query string from array
     */
    protected function buildQueryString(array $params): string
    {
        return http_build_query($params);
    }
}
