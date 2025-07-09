<?php

// Bootstrap file for PHPUnit tests

// Set up error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define project root
define('PROJECT_ROOT', dirname(__DIR__, 2));

// Load Composer autoloader
require_once PROJECT_ROOT . '/vendor/autoload.php';

// Set up environment variables for testing
$_ENV['APP_ENV'] = 'testing';
$_ENV['LMS_OLLAMA_ENABLED'] = 'true';
$_ENV['LMS_OLLAMA_HOST'] = 'http://localhost:11434';
$_ENV['LMS_OLLAMA_MODEL'] = 'llama2';
$_ENV['LMS_GEMINI_ENABLED'] = 'false';
$_ENV['LMS_GEMINI_API_KEY'] = '';
$_ENV['LMS_TENCENT_EDU_ENABLED'] = 'false';
$_ENV['LMS_TENCENT_EDU_APP_ID'] = '';
$_ENV['LMS_TENCENT_EDU_SECRET_KEY'] = '';

// Define constants that might be needed
if (!defined('APP_PATH')) {
    define('APP_PATH', PROJECT_ROOT . '/IamLab');
}

if (!defined('TMP_PATH')) {
    define('TMP_PATH', sys_get_temp_dir());
}

// Set up any global test helpers or mocks here
class TestHelper
{
    /**
     * Create a mock HTTP response for testing external API calls
     */
    public static function createMockHttpResponse(array $data, int $httpCode = 200): array
    {
        return [
            'data' => $data,
            'http_code' => $httpCode,
            'success' => $httpCode >= 200 && $httpCode < 300
        ];
    }

    /**
     * Get test configuration for integrations
     */
    public static function getTestConfig(): array
    {
        return [
            'gemini' => [
                'enabled' => false,
                'api_key' => 'test_api_key',
                'model' => 'gemini-pro'
            ],
            'ollama' => [
                'enabled' => true,
                'host' => 'http://localhost:11434',
                'model' => 'llama2'
            ],
            'tencent_edu' => [
                'enabled' => false,
                'app_id' => 'test_app_id',
                'secret_key' => 'test_secret_key',
                'region' => 'ap-beijing'
            ]
        ];
    }

    /**
     * Create a mock integration response
     */
    public static function createMockIntegrationResponse(bool $success = true, string $content = 'Test content'): array
    {
        if ($success) {
            return [
                'success' => true,
                'content' => $content,
                'model' => 'test_model',
                'timestamp' => time()
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Test error message',
                'timestamp' => time()
            ];
        }
    }
}

echo "PHPUnit bootstrap loaded successfully\n";