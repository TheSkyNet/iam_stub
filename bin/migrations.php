<?php

use Phalcon\Config\Config;

use function IamLab\Core\Helpers\env;
use function IamLab\Core\Helpers\loadEnv;

$rootPath = dirname(__DIR__);
require_once $rootPath . '/vendor/autoload.php';
loadEnv($rootPath . '/.env');

return new Config([
    'database' => [
        'adapter' => 'Mysql',
        'host' => env('DB_HOST', 'mysql'),
        'port' => env('DB_PORT', 3306),
        'username' => env('DB_USERNAME', 'phalcons'),
        'password' => env('DB_PASSWORD', 'phalcons'),
        'dbname' => env('DB_NAME', env('DB_DATABASE', 'phalcons')),
        'charset' => env('DB_CHARSET', 'utf8mb4'),
    ],
    'application' => [
        'logInDb' => true,
        'no-auto-increment' => true,
        'skip-ref-schema' => true,
        'skip-foreign-checks' => true,
        'migrationsDir' => $rootPath . '/IamLab/Migrations/',
        'migrationsTsBased' => false, // true - Use TIMESTAMP as version name, false - use versions
        'exportDataFromTables' => [
            // Tables names
            // Attention! It will export data every new migration
        ],
    ],
]);
