<?php
// /bin/seeder.php

use IamLab\Migrations\Seeders\PackageSeeder;
use IamLab\Migrations\Seeders\PostSeeder;
use IamLab\Migrations\Seeders\ProjectSeeder;
use IamLab\Model\User;
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use function App\Core\Helpers\loadEnv;

// Set paths
define('APP_PATH', realpath(__DIR__ . '/../IamLab'));
define('ROOT_PATH', realpath(__DIR__ . '/..'));

// Include composer autoloader
require_once ROOT_PATH . '/vendor/autoload.php';

// Load environment variables
loadEnv(ROOT_PATH.'/.env');

// Set error reporting based on environment
if (\App\Core\Helpers\env('APP_DEBUG') == 'debug') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Create DI container
$di = new FactoryDefault();

// Include Services
include APP_PATH . '/config/services.php';

// Get the loader from DI
$di->getLoader();

// Create application instance
$app = new Micro($di);

// Parse command line arguments
$options = getopt('', ['email:', 'password:']);
$email = $options['email'] ?? null;
$password = $options['password'] ?? null;

// Validate required parameters
if (!$email || !$password) {
    echo "Error: Email and password are required.\n";
    echo "Usage: php seeder.php --email=user@example.com --password=yourpassword\n";
    exit(1);
}

try {
    // Start transaction
    $di->get('db')->begin();

    // Create user
    $user = new User();
    $user->setEmail($email);
    $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
    $user->setName(explode('@', $email)[0]);
    $user->setKey(bin2hex(random_bytes(32)));

    if ($user->save()) {
        echo "Successfully created user: {$email}\n";
    } else {
        echo "Failed to create user: {$email}\n";
        foreach ($user->getMessages() as $message) {
            echo $message->getMessage() . "\n";
        }
        $di->get('db')->rollback();
        exit(1);
    }

    // Run post seeder
    echo "\nStarting to seed posts...\n";
    $postSeeder = new PostSeeder();
    $postSeeder->run();
    echo "\nStarting to seed packages...\n";
    $packageSeeder = new PackageSeeder();
    $packageSeeder->run();
    echo "\nStarting to seed projects...\n";
    $projectSeeder = new ProjectSeeder();
    $projectSeeder->run();
    echo "\nStarting to seed site settings...\n";
    $siteSettingsSeeder = new \IamLab\Migrations\Seeders\SiteSettingsSeeder();
    $siteSettingsSeeder->run();

    // Commit transaction
    $di->get('db')->commit();
    echo "\nSeeding completed successfully!\n";

} catch (Exception $e) {
    // Rollback on error
    $di->get('db')->rollback();
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}