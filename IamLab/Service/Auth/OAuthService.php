<?php

namespace IamLab\Service\Auth;

use IamLab\Core\API\aAPI;
use IamLab\Model\User;
use Exception;
use Phalcon\Http\Client\Provider\Curl;
use function App\Core\Helpers\config;

abstract class OAuthService extends aAPI
{
    protected string $provider;
    protected array $config;
    
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
     * Make HTTP request
     */
    protected function makeRequest(string $url, array $data = [], string $method = 'GET', array $headers = []): array
    {
        $curl = new Curl();
        
        $defaultHeaders = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];
        
        $headers = array_merge($defaultHeaders, $headers);
        
        try {
            if ($method === 'POST') {
                $response = $curl->post($url, json_encode($data), $headers);
            } else {
                $response = $curl->get($url, $headers);
            }
            
            $result = json_decode($response->body, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response from OAuth provider');
            }
            
            return $result;
        } catch (Exception $e) {
            throw new Exception('OAuth request failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Build query string from array
     */
    protected function buildQueryString(array $params): string
    {
        return http_build_query($params);
    }
}