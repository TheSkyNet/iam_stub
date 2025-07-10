<?php

namespace IamLab\Service\Auth;

use Exception;

class FacebookOAuthService extends OAuthService
{
    private const AUTHORIZATION_URL = 'https://www.facebook.com/v18.0/dialog/oauth';
    private const TOKEN_URL = 'https://graph.facebook.com/v18.0/oauth/access_token';
    private const USER_INFO_URL = 'https://graph.facebook.com/v18.0/me';
    
    public function __construct()
    {
        parent::__construct('facebook');
    }
    
    /**
     * Get the authorization URL for Facebook OAuth
     */
    public function getAuthorizationUrl(string $state = null): string
    {
        if (!$state) {
            $state = $this->generateState();
        }
        
        $params = [
            'client_id' => $this->config['client_id'],
            'redirect_uri' => $this->config['redirect_uri'],
            'scope' => implode(',', $this->config['scopes']),
            'response_type' => 'code',
            'state' => $state,
            'auth_type' => 'rerequest'
        ];
        
        return self::AUTHORIZATION_URL . '?' . $this->buildQueryString($params);
    }
    
    /**
     * Exchange authorization code for access token
     */
    public function getAccessToken(string $code, string $state = null): array
    {
        $params = [
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'code' => $code,
            'redirect_uri' => $this->config['redirect_uri']
        ];
        
        $url = self::TOKEN_URL . '?' . $this->buildQueryString($params);
        
        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json'
                ]
            ]);
            
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            
            if ($httpCode !== 200) {
                throw new Exception('Failed to get access token from Facebook');
            }
            
            $result = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE || !isset($result['access_token'])) {
                throw new Exception('Invalid response from Facebook token endpoint');
            }
            
            return $result;
        } catch (Exception $e) {
            throw new Exception('Facebook OAuth token exchange failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get user information from Facebook
     */
    public function getUserInfo(string $accessToken): array
    {
        $fields = [
            'id',
            'name',
            'email',
            'first_name',
            'last_name',
            'picture.type(large)',
            'verified',
            'locale',
            'timezone',
            'updated_time'
        ];
        
        $params = [
            'fields' => implode(',', $fields),
            'access_token' => $accessToken
        ];
        
        $url = self::USER_INFO_URL . '?' . $this->buildQueryString($params);
        
        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json'
                ]
            ]);
            
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            
            if ($httpCode !== 200) {
                throw new Exception('Failed to get user info from Facebook');
            }
            
            $userInfo = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response from Facebook user info endpoint');
            }
            
            // Check if email is available
            if (empty($userInfo['email'])) {
                throw new Exception('Email permission not granted by Facebook user');
            }
            
            // Normalize user data
            return [
                'id' => (string)$userInfo['id'],
                'email' => $userInfo['email'],
                'name' => $userInfo['name'] ?? '',
                'first_name' => $userInfo['first_name'] ?? '',
                'last_name' => $userInfo['last_name'] ?? '',
                'avatar' => $userInfo['picture']['data']['url'] ?? '',
                'verified' => $userInfo['verified'] ?? false,
                'locale' => $userInfo['locale'] ?? '',
                'timezone' => $userInfo['timezone'] ?? null
            ];
        } catch (Exception $e) {
            throw new Exception('Facebook OAuth user info request failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get long-lived access token from Facebook
     */
    public function getLongLivedToken(string $shortLivedToken): array
    {
        $params = [
            'grant_type' => 'fb_exchange_token',
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'fb_exchange_token' => $shortLivedToken
        ];
        
        $url = self::TOKEN_URL . '?' . $this->buildQueryString($params);
        
        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json'
                ]
            ]);
            
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            
            if ($httpCode !== 200) {
                throw new Exception('Failed to get long-lived token from Facebook');
            }
            
            $result = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE || !isset($result['access_token'])) {
                throw new Exception('Invalid response from Facebook long-lived token endpoint');
            }
            
            return $result;
        } catch (Exception $e) {
            throw new Exception('Facebook OAuth long-lived token exchange failed: ' . $e->getMessage());
        }
    }
}