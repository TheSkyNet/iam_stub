<?php

// Bootstrap file for PHPUnit tests

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Set up test environment
$_ENV['APP_ENV'] = 'testing';

// Set default LMS configuration for testing
$_ENV['LMS_OLLAMA_ENABLED'] = '1';
$_ENV['LMS_OLLAMA_HOST'] = 'http://ollama:11434';
$_ENV['LMS_OLLAMA_MODEL'] = 'llama2';
$_ENV['LMS_GEMINI_ENABLED'] = '0';
$_ENV['LMS_GEMINI_API_KEY'] = '';
$_ENV['LMS_TENCENT_EDU_ENABLED'] = '0';
$_ENV['LMS_TENCENT_EDU_APP_ID'] = '';
$_ENV['LMS_TENCENT_EDU_SECRET_KEY'] = '';

// Define constants if not already defined
if (!defined('APP_PATH')) {
    define('APP_PATH', realpath(__DIR__ . '/../IamLab'));
}

if (!defined('TMP_PATH')) {
    define('TMP_PATH', sys_get_temp_dir());
}

echo "PHPUnit Bootstrap: Test environment initialized\n";

// Provide a minimal Phalcon DI container for unit tests that touch Models
// This prevents "A dependency injection container is required" exceptions
try {
    if (class_exists('Phalcon\\Di\\Di')) {
        $di = new \Phalcon\Di\Di();
        \Phalcon\Di\Di::setDefault($di);

        // Minimal services required by Phalcon\Mvc\Model
        if (class_exists('Phalcon\\Mvc\\Model\\Manager')) {
            $di->setShared('modelsManager', function () {
                return new \Phalcon\Mvc\Model\Manager();
            });
        }
        if (class_exists('Phalcon\\Mvc\\Model\\Metadata\\Memory')) {
            $di->setShared('modelsMetadata', function () {
                return new \Phalcon\Mvc\Model\Metadata\Memory();
            });
        }

        // Optional config service stub to satisfy code that looks it up
        $di->set('config', function () {
            return new class {
                public function get(string $key, $default = null)
                {
                    // In tests we default to env-stubbed values
                    return $default;
                }
            };
        });
    }
} catch (\Throwable $e) {
    // If Phalcon DI is not available, proceed; tests that need it will be skipped or use fallbacks
}
