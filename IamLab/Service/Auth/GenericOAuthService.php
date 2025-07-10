<?php

namespace IamLab\Service\Auth;

use Exception;

class GenericOAuthService extends OAuthService
{
    public function __construct()
    {
        parent::__construct('generic');
        
        // Validate required configuration
        $requiredFields = ['authorization_url', 'token_url', 'user_info_url'];
        foreach ($requiredFields as $field) {
            if (empty($this->config[$field])) {
                throw new Exception("Generic OAuth configuration missing required field: {$field}");
            }
        }
    }
    
    /**
     * Get the authorization URL for Generic OAuth
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
            'response_type' => 'code',
            'state' => $state
        ];
        
        return $this->config['authorization_url'] . '?' . $this->buildQueryString($params);
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
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->config['redirect_uri']
        ];
        
        $headers = [
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded'
        ];
        
        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $this->config['token_url'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($data),
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_SSL_VERIFYPEER => false
            ]);
            
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            
            if ($httpCode !== 200) {
                throw new Exception('Failed to get access token from Generic OAuth provider');
            }
            
            $result = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE || !isset($result['access_token'])) {
                throw new Exception('Invalid response from Generic OAuth token endpoint');
            }
            
            return $result;
        } catch (Exception $e) {
            throw new Exception('Generic OAuth token exchange failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get user information from Generic OAuth provider
     */
    public function getUserInfo(string $accessToken): array
    {
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Accept: application/json'
        ];
        
        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $this->config['user_info_url'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_SSL_VERIFYPEER => false
            ]);
            
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            
            if ($httpCode !== 200) {
                throw new Exception('Failed to get user info from Generic OAuth provider');
            }
            
            $userInfo = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response from Generic OAuth user info endpoint');
            }
            
            // Normalize user data - try common field names
            return $this->normalizeUserData($userInfo);
        } catch (Exception $e) {
            throw new Exception('Generic OAuth user info request failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Normalize user data from various OAuth providers
     */
    private function normalizeUserData(array $userInfo): array
    {
        // Try to extract common fields from various possible field names
        $normalized = [
            'id' => $this->extractField($userInfo, ['id', 'sub', 'user_id', 'uid']),
            'email' => $this->extractField($userInfo, ['email', 'mail', 'email_address']),
            'name' => $this->extractField($userInfo, ['name', 'display_name', 'full_name', 'displayName']),
            'first_name' => $this->extractField($userInfo, ['given_name', 'first_name', 'firstName']),
            'last_name' => $this->extractField($userInfo, ['family_name', 'last_name', 'lastName', 'surname']),
            'username' => $this->extractField($userInfo, ['username', 'login', 'preferred_username']),
            'avatar' => $this->extractField($userInfo, ['picture', 'avatar', 'avatar_url', 'profile_image_url']),
            'locale' => $this->extractField($userInfo, ['locale', 'language', 'lang']),
            'verified' => $this->extractField($userInfo, ['email_verified', 'verified', 'is_verified'])
        ];
        
        // Ensure we have at least an ID and email
        if (empty($normalized['id'])) {
            throw new Exception('User ID not found in OAuth response');
        }
        
        if (empty($normalized['email'])) {
            throw new Exception('Email not found in OAuth response');
        }
        
        // Convert ID to string
        $normalized['id'] = (string)$normalized['id'];
        
        // If name is empty, try to construct it from first/last name
        if (empty($normalized['name']) && (!empty($normalized['first_name']) || !empty($normalized['last_name']))) {
            $normalized['name'] = trim($normalized['first_name'] . ' ' . $normalized['last_name']);
        }
        
        // If name is still empty, use username or email
        if (empty($normalized['name'])) {
            $normalized['name'] = $normalized['username'] ?: explode('@', $normalized['email'])[0];
        }
        
        return $normalized;
    }
    
    /**
     * Extract field value from user info using multiple possible field names
     */
    private function extractField(array $data, array $possibleFields): string
    {
        foreach ($possibleFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                // Handle nested objects (like picture.data.url)
                if (is_array($data[$field]) && isset($data[$field]['data']['url'])) {
                    return $data[$field]['data']['url'];
                }
                
                // Handle boolean values
                if (is_bool($data[$field])) {
                    return $data[$field] ? '1' : '0';
                }
                
                return (string)$data[$field];
            }
        }
        
        return '';
    }
    
    /**
     * Get supported field mappings for documentation
     */
    public static function getSupportedFieldMappings(): array
    {
        return [
            'id' => ['id', 'sub', 'user_id', 'uid'],
            'email' => ['email', 'mail', 'email_address'],
            'name' => ['name', 'display_name', 'full_name', 'displayName'],
            'first_name' => ['given_name', 'first_name', 'firstName'],
            'last_name' => ['family_name', 'last_name', 'lastName', 'surname'],
            'username' => ['username', 'login', 'preferred_username'],
            'avatar' => ['picture', 'avatar', 'avatar_url', 'profile_image_url'],
            'locale' => ['locale', 'language', 'lang'],
            'verified' => ['email_verified', 'verified', 'is_verified']
        ];
    }
}