<?php

namespace IamLab\Service\Auth;

use Exception;

class GoogleOAuthService extends OAuthService
{
    private const string AUTHORIZATION_URL = 'https://accounts.google.com/o/oauth2/v2/auth';

    private const string TOKEN_URL = 'https://oauth2.googleapis.com/token';

    private const string USER_INFO_URL = 'https://www.googleapis.com/oauth2/v2/userinfo';

    public function __construct()
    {
        parent::__construct('google');
    }

    /**
     * Get the authorization URL for Google OAuth
     */
    #[\Override]
    public function getAuthorizationUrl(?string $state = null): string
    {
        if (!$state) {
            $state = $this->generateState();
        }

        $params = [
            'client_id' => $this->config['client_id'],
            'redirect_uri' => $this->config['redirect_uri'],
            'scope' => implode(' ', $this->config['scopes']),
            'response_type' => 'code',
            'state' => $state,
            'access_type' => 'offline',
            'prompt' => 'consent'
        ];

        return self::AUTHORIZATION_URL . '?' . $this->buildQueryString($params);
    }

    /**
     * Exchange authorization code for access token
     */
    #[\Override]
    public function getAccessToken(string $code, ?string $state = null): array
    {
        $data = [
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->config['redirect_uri']
        ];

        $headers = [
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
                CURLOPT_SSL_VERIFYPEER => false
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($httpCode !== 200) {
                throw new Exception('Failed to get access token from Google');
            }

            $result = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE || !isset($result['access_token'])) {
                throw new Exception('Invalid response from Google token endpoint');
            }

            return $result;
        } catch (Exception $exception) {
            throw new Exception('Google OAuth token exchange failed: ' . $exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * Get user information from Google
     */
    #[\Override]
    public function getUserInfo(string $accessToken): array
    {
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Accept: application/json'
        ];

        try {
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
                throw new Exception('Failed to get user info from Google');
            }

            $userInfo = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response from Google user info endpoint');
            }

            // Normalize user data
            return [
                'id' => $userInfo['id'],
                'email' => $userInfo['email'],
                'name' => $userInfo['name'] ?? '',
                'first_name' => $userInfo['given_name'] ?? '',
                'last_name' => $userInfo['family_name'] ?? '',
                'avatar' => $userInfo['picture'] ?? '',
                'verified_email' => $userInfo['verified_email'] ?? false
            ];
        } catch (Exception $exception) {
            throw new Exception('Google OAuth user info request failed: ' . $exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
