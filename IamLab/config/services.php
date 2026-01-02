<?php

use IamLab\Service\Filepond\FilepondService;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Autoload\Loader;
use IamLab\Service\Auth\AuthService;
use Phalcon\Logger\Logger;
use Phalcon\Logger\Adapter\Stream as FileLogger;
use Phalcon\Logger\Formatter\Line as LineFormatter;
use Phalcon\Http\Response;
use Phalcon\Mvc\View\Simple;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;
use function App\Core\Helpers\config;

/**
 * Shared configuration service
 */
/** @var Phalcon\Di\FactoryDefault $di */
$di->setShared(
    'config',
    function () {
        return include APP_PATH . "/config/config.php";
    }
);

/**
 * Shared loader service
 */
$di->setShared(
    'loader',
    function () {
        $loader = new Loader();
        $config = $this->getConfig();


        $loader->setDirectories(
            [
                $config->application->modelsDir,
            ]
        )->register();

        return $loader;
    }
);

/**
 * Sets the view component
 */
$di->set(
    'view',
    function () {
        $config = $this->getConfig();

        $view = new Simple();
        $view->setViewsDir($config->application->viewsDir);

        return $view;
    }
);

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared(
    'url',
    function () {
        $config = $this->getConfig();

        $url = new UrlResolver();
        $url->setBaseUri($config->application->baseUri);

        return $url;
    }
);
$di->setShared(
    'logger',
    function () {
        $cfg = config('logger');
        // If disabled, return a basic logger to /dev/null to avoid null checks
        $path = $cfg['path'] ?? '/var/www/html/files/logs/app.log';
        $enabled = (bool)($cfg['enabled'] ?? true);
        $level = strtolower((string)($cfg['level'] ?? 'debug'));
        $format = (string)($cfg['format'] ?? '[%date%][%level%] %message%');

        // Ensure directory exists
        $dir = dirname($path);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $adapter = new FileLogger($enabled ? $path : '/dev/null');
        $formatter = new LineFormatter($format, 'Y-m-d H:i:s');
        $adapter->setFormatter($formatter);

        $logger = new Logger('app');
        $logger->addAdapter('file', $adapter);

        return $logger;
    }
);
$di->setShared(
    'filepond',
    function () {
        return new FilepondService();
    }
);
$di->setShared(
    'file',
    function () {
        $adapter = new LocalFilesystemAdapter(FILE_PATH);

        return new League\Flysystem\Filesystem($adapter);
    }
);
$di->setShared(
    'tmp',
    function () {

        $adapter = new LocalFilesystemAdapter(TMP_DISK);
        return new League\Flysystem\Filesystem($adapter);
    }
);
$di->setShared(
    'fs',
    function () {
        $adapter = new LocalFilesystemAdapter(ROOT_DISK);
        return new League\Flysystem\Filesystem($adapter);
    }
);
/**
 * Database connection is created based in the parameters defined in the
 * configuration file
 */
$di->setShared(
    'db',
    function () {
        $config = config('database');
        return new Mysql(
            [
                'host' => $config['host'],
                'username' => $config['username'],
                'password' => $config['password'],
                'dbname' => $config['dbname'],
            ]
        );
    }
);
// Set the models cache service
$di->set(
    'modelsCache',
    function () {

        // Cache data for one day by default
        $frontCache = new Memcache(
            [
                "lifetime" => 86400,
            ]
        );

        // Memcached connection settings
        return new Memory(
            $frontCache,
            [
                "host" => "localhost",
                "port" => "11211",
            ]
        );
    }
);

$di->setShared(
    'authService',
    function () {
        return new  AuthService();
    }
);
$di->setShared(
    'session',
    function () {

        $session = new Manager();
        $files = new Stream(
            [
                'savePath' => '/tmp',
            ]
        );
        $session->setAdapter($files)->start();
        return $session;
    }
);

$di->setShared(
    'isAuthenticated',
    function () {
        if (!(new AuthService())->isAuthenticated()) {
            $response = new Response();
            $response->redirect('/auth');
            $response->send();

        }
        return true;
    }
);


// Server-Sent Events: shared emitter factory
$di->set(
    'sseEmitter',
    function () {
        return new \IamLab\Core\SSE\SseEmitter(new \IamLab\Core\SSE\PhpOutputWriter());
    }
);

