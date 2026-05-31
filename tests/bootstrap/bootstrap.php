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

echo "PHPUnit bootstrap loaded successfully\n";
