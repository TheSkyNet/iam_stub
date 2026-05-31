<?php

use Phalcon\Di\Injectable;
use Phalcon\Di\Di;
use Phalcon\Mvc\Model\Manager;
use Phalcon\Mvc\Model\Metadata\Memory;

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

// Provide a stub for Phalcon\Di\Injectable if Phalcon is not installed
// This allows aAPI to extend it without breaking PSR-12 or causing fatal errors in tests
if (!class_exists(Injectable::class)) {
    eval('namespace Phalcon\Di { abstract class Injectable {} }');
}

echo "PHPUnit Bootstrap: Test environment initialized\n";

// Provide a minimal Phalcon DI container for unit tests that touch Models
// This prevents "A dependency injection container is required" exceptions
try {
    if (class_exists(Di::class)) {
        $di = new Di();
        Di::setDefault($di);

        // Minimal services required by Phalcon\Mvc\Model
        if (class_exists(Manager::class)) {
            $di->setShared('modelsManager', fn(): Manager => new Manager());
        }

        if (class_exists(Memory::class)) {
            $di->setShared('modelsMetadata', fn(): Memory => new Memory());
        }

        // Optional config service stub to satisfy code that looks it up
        $di->set('config', fn(): object => new class {
            public function get(string $key, $default = null)
            {
                // In tests we default to env-stubbed values
                return $default;
            }
        });
    }
} catch (\Throwable) {
    // If Phalcon DI is not available, proceed; tests that need it will be skipped or use fallbacks
}
