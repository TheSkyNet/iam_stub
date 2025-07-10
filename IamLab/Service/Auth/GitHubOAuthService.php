<?php

namespace IamLab\Service\Auth;

use Exception;

class GitHubOAuthService extends OAuthService
{
    private const AUTHORIZATION_URL = 'https://github.com/login/oauth/authorize';
    private const TOKEN_URL = 'https://github.com/login/oauth/access_token';
    private const USER_INFO_URL = 'https://api.github.com/user';
    private const USER_EMAILS_URL = 'https://api.github.com/user/emails';
    
    public function __construct()
    {
        parent::__construct('github');
    }
    
    /**
     * Get the authorization URL for GitHub OAuth
     */
    public function getAuthorizationUrl(string $state = null): string
    {
        if (!$state) {
            $state = $this->generateState();
        }
        
        $params = [
            'client_id' => $this->config['client_id'],
            'redirect_uri' => $this->config['redirect_uri'],
            'scope' => implode(' ', $this->config['scopes']),
            'state' => $state,
            'allow_signup' => 'true'
        ];
        
        return self::AUTHORIZATION_URL . '?' . $this->buildQueryString($params);
    }
    
    /**
     * Exchange authorization code for access token
     */
    public function getAccessToken(string $code, string $state = null): array
    {
        $data = [
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'code' => $code,
            'redirect_uri' => $this->config['redirect_uri']
        ];
        
        $headers = [
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded'
        ];
        
        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => self::TOKEN_URL,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($data),
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => 'IAMLab-OAuth-App'
            ]);
            
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            
            if ($httpCode !== 200) {
                throw new Exception('Failed to get access token from GitHub');
            }
            
            $result = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE || !isset($result['access_token'])) {
                throw new Exception('Invalid response from GitHub token endpoint');
            }
            
            return $result;
        } catch (Exception $e) {
            throw new Exception('GitHub OAuth token exchange failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get user information from GitHub
     */
    public function getUserInfo(string $accessToken): array
    {
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Accept: application/vnd.github.v3+json',
            'User-Agent: IAMLab-OAuth-App'
        ];
        
        try {
            // Get user basic info
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => self::USER_INFO_URL,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_SSL_VERIFYPEER => false
            ]);
            
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            
            if ($httpCode !== 200) {
                throw new Exception('Failed to get user info from GitHub');
            }
            
            $userInfo = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response from GitHub user info endpoint');
            }
            
            // Get user emails if email is not public
            $email = $userInfo['email'];
            if (empty($email)) {
                $email = $this->getUserPrimaryEmail($accessToken);
            }
            
            // Normalize user data
            return [
                'id' => (string)$userInfo['id'],
                'email' => $email,
                'name' => $userInfo['name'] ?? $userInfo['login'],
                'username' => $userInfo['login'],
                'avatar' => $userInfo['avatar_url'] ?? '',
                'bio' => $userInfo['bio'] ?? '',
                'location' => $userInfo['location'] ?? '',
                'company' => $userInfo['company'] ?? ''
            ];
        } catch (Exception $e) {
            throw new Exception('GitHub OAuth user info request failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get user's primary email from GitHub
     */
    private function getUserPrimaryEmail(string $accessToken): string
    {
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Accept: application/vnd.github.v3+json',
            'User-Agent: IAMLab-OAuth-App'
        ];
        
        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => self::USER_EMAILS_URL,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_SSL_VERIFYPEER => false
            ]);
            
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            
            if ($httpCode !== 200) {
                throw new Exception('Failed to get user emails from GitHub');
            }
            
            $emails = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response from GitHub emails endpoint');
            }
            
            // Find primary email
            foreach ($emails as $emailData) {
                if ($emailData['primary'] && $emailData['verified']) {
                    return $emailData['email'];
                }
            }
            
            // Fallback to first verified email
            foreach ($emails as $emailData) {
                if ($emailData['verified']) {
                    return $emailData['email'];
                }
            }
            
            throw new Exception('No verified email found for GitHub user');
        } catch (Exception $e) {
            throw new Exception('GitHub OAuth email request failed: ' . $e->getMessage());
        }
    }
}