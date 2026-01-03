<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro;
use IamLab\Service\Auth\AuthService;
use Phalcon\Http\Response;
use Phalcon\Mvc\View\Simple;
use Phalcon\Mvc\Url as UrlResolver;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use function App\Core\Helpers\loadEnv;


include "../vendor/autoload.php";
define('APP_PATH', realpath('../IamLab'));
define('ROOT_PATH', realpath('../'));
if (!file_exists(__DIR__ . '/' . $_SERVER['REQUEST_URI'])) {
    $_GET['_url'] = $_SERVER['REQUEST_URI'];
}

// Load environment before configuring error/exception handling
loadEnv(ROOT_PATH.'/.env');

// Normalize APP_DEBUG and APP_ENV
$appEnv = strtolower((string) \App\Core\Helpers\env('APP_ENV', 'production'));
$debugRaw = (string) \App\Core\Helpers\env('APP_DEBUG', 'false');
$appDebug = in_array(strtolower($debugRaw), ['1','true','on','yes','debug'], true);

// Enable detailed error pages (Whoops) only when explicitly in debug mode
if ($appDebug) {
    $whoops = new Run();
    $whoops->pushHandler(new PrettyPageHandler());
    $whoops->register();

    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    // In non-debug environments, avoid leaking details
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
}


$di = new FactoryDefault();

/**
 * Include Services
 */
include APP_PATH . '/config/services.php';

/**
 * Call the autoloader service.  We don't need to keep the results.
 */
$di->getLoader();

/**
 * Starting the application
 * Assign service locator to the application
 */
$app = new Micro($di);

/**
 * Include Application
 */
include APP_PATH . '/app.php';

/**
 * Handle the request
 */
$app->handle($_SERVER["REQUEST_URI"]);

