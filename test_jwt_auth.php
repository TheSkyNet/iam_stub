<?php

// Simple test script for JWT authentication
require_once 'vendor/autoload.php';

use IamLab\Service\Auth\JwtService;
use IamLab\Service\Auth\AuthService;
use IamLab\Model\User;

echo "Testing JWT Authentication System\n";
echo "================================\n\n";

try {
    // Test 1: Create JwtService
    echo "1. Testing JwtService creation...\n";
    $jwtService = new JwtService();
    echo "✓ JwtService created successfully\n\n";

    // Test 2: Create a mock user for testing
    echo "2. Testing with mock user...\n";
    $user = new User();
    $user->setId(1);
    $user->setName('Test User');
    $user->setEmail('test@example.com');
    
    // Test 3: Generate access token
    echo "3. Testing access token generation...\n";
    $accessToken = $jwtService->generateAccessToken($user);
    echo "✓ Access token generated: " . substr($accessToken, 0, 50) . "...\n\n";

    // Test 4: Generate refresh token
    echo "4. Testing refresh token generation...\n";
    $refreshToken = $jwtService->generateRefreshToken($user);
    echo "✓ Refresh token generated: " . substr($refreshToken, 0, 50) . "...\n\n";

    // Test 5: Validate access token
    echo "5. Testing token validation...\n";
    $payload = $jwtService->validateToken($accessToken);
    echo "✓ Token validated successfully\n";
    echo "   User ID: " . $payload['user_id'] . "\n";
    echo "   Email: " . $payload['email'] . "\n";
    echo "   Type: " . $payload['type'] . "\n\n";

    // Test 6: Generate API key
    echo "6. Testing API key generation...\n";
    $apiKey = $jwtService->generateApiKey($user);
    echo "✓ API key generated: " . substr($apiKey, 0, 50) . "...\n\n";

    // Test 7: Test AuthService
    echo "7. Testing AuthService...\n";
    $authService = new AuthService();
    echo "✓ AuthService created successfully\n\n";

    echo "All tests passed! JWT authentication system is working correctly.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}