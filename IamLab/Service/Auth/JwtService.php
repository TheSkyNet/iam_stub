<?php

namespace IamLab\Service\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use IamLab\Model\User;
use Phalcon\Di\Injectable;
use Exception;

class JwtService extends Injectable
{
    private string $secretKey;
    private string $algorithm;
    private int $accessTokenExpiry;
    private int $refreshTokenExpiry;
    private string $issuer;
    private string $audience;

    public function __construct()
    {
        // Get JWT configuration from Phalcon config
        $config = $this->getDI()->getShared('config');
        $jwtConfig = $config->jwt ?? null;

        if (!$jwtConfig) {
            throw new Exception('JWT configuration not found in config');
        }

        $this->secretKey = $jwtConfig->secret;
        $this->algorithm = $jwtConfig->algorithm;
        $this->accessTokenExpiry = $jwtConfig->access_token_expiry;
        $this->refreshTokenExpiry = $jwtConfig->refresh_token_expiry;
        $this->issuer = $jwtConfig->issuer;
        $this->audience = $jwtConfig->audience;
    }

    /**
     * Generate access token for user
     */
    public function generateAccessToken(User $user): string
    {
        $payload = [
            'iss' => $this->issuer, // Issuer
            'aud' => $this->audience, // Audience
            'iat' => time(), // Issued at
            'exp' => time() + $this->accessTokenExpiry, // Expiration
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'type' => 'access'
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    /**
     * Generate refresh token for user
     */
    public function generateRefreshToken(User $user): string
    {
        $payload = [
            'iss' => $this->issuer,
            'aud' => $this->audience,
            'iat' => time(),
            'exp' => time() + $this->refreshTokenExpiry,
            'user_id' => $user->getId(),
            'type' => 'refresh'
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    /**
     * Validate and decode JWT token
     */
    public function validateToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return (array) $decoded;
        } catch (ExpiredException $e) {
            throw new Exception('Token has expired');
        } catch (SignatureInvalidException $e) {
            throw new Exception('Invalid token signature');
        } catch (Exception $e) {
            throw new Exception('Invalid token: ' . $e->getMessage());
        }
    }

    /**
     * Extract token from Authorization header
     */
    public function extractTokenFromHeader(): ?string
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (!$authHeader) {
            return null;
        }

        // Check for Bearer token format
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Get user from token
     */
    public function getUserFromToken(string $token): ?User
    {
        try {
            $payload = $this->validateToken($token);

            if ($payload['type'] !== 'access') {
                throw new Exception('Invalid token type');
            }

            return User::findFirstById($payload['user_id']);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Refresh access token using refresh token
     */
    public function refreshAccessToken(string $refreshToken): ?array
    {
        try {
            $payload = $this->validateToken($refreshToken);

            if ($payload['type'] !== 'refresh') {
                throw new Exception('Invalid token type for refresh');
            }

            $user = User::findFirstById($payload['user_id']);
            if (!$user) {
                throw new Exception('User not found');
            }

            $newAccessToken = $this->generateAccessToken($user);
            $newRefreshToken = $this->generateRefreshToken($user);

            return [
                'access_token' => $newAccessToken,
                'refresh_token' => $newRefreshToken,
                'expires_in' => $this->accessTokenExpiry,
                'token_type' => 'Bearer'
            ];
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Generate API key for user
     */
    public function generateApiKey(User $user): string
    {
        $payload = [
            'iss' => $this->issuer,
            'aud' => $this->audience,
            'iat' => time(),
            'user_id' => $user->getId(),
            'type' => 'api_key'
            // No expiration for API keys
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    /**
     * Validate API key
     */
    public function validateApiKey(string $apiKey): ?User
    {
        try {
            $payload = $this->validateToken($apiKey);

            if ($payload['type'] !== 'api_key') {
                throw new Exception('Invalid token type');
            }

            return User::findFirstById($payload['user_id']);
        } catch (Exception $e) {
            return null;
        }
    }
}
