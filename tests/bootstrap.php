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
