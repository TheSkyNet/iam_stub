<?php

use Phalcon\Encryption\Crypt;
use League\Flysystem\Filesystem;
use IamLab\Core\SSE\SseEmitter;
use IamLab\Core\SSE\PhpOutputWriter;
use Phalcon\Di\FactoryDefault;
use IamLab\Service\Filepond\FilepondService;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Phalcon\Acl\Adapter\Memory as AclMemory;
use Phalcon\Cache\Adapter\Redis as RedisAdapter;
use Phalcon\Storage\SerializerFactory;
use Phalcon\Cache\Cache;
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

use function IamLab\Core\Helpers\config;

/** @var FactoryDefault $di */
$di->setShared(
    'config',
    fn() => include APP_PATH . "/config/config.php"
);

/**
 * Shared encryption service
 */
$di->setShared(
    'crypt',
    function (): Crypt {
        $config = $this->getConfig();
        $crypt = new Crypt();
        $crypt->setKey($config->app->encryption_key);
        return $crypt;
    }
);

/**
 * Shared loader service
 */
$di->setShared(
    'loader',
    function (): Loader {
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
    function (): Simple {
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
    function (): UrlResolver {
        $config = $this->getConfig();

        $url = new UrlResolver();
        $url->setBaseUri($config->application->baseUri);

        return $url;
    }
);
$di->setShared(
    'logger',
    function (): Logger {
        $cfg = config('logger');
        // If disabled, return a basic logger to /dev/null to avoid null checks
        $path = $cfg['path'] ?? '/var/www/html/files/logs/app.log';
        $enabled = (bool)($cfg['enabled'] ?? true);
        $level = strtolower((string)($cfg['level'] ?? 'debug'));
        $format = (string)($cfg['format'] ?? '[%date%][%level%] %message%');

        // Ensure directory exists
        $dir = dirname((string) $path);
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
    fn(): FilepondService => new FilepondService()
);
$di->setShared(
    'file',
    function (): Filesystem {
        $adapter = new LocalFilesystemAdapter(FILE_PATH);

        return new Filesystem($adapter);
    }
);
$di->setShared(
    'tmp',
    function (): Filesystem {

        $adapter = new LocalFilesystemAdapter(TMP_DISK);
        return new Filesystem($adapter);
    }
);
$di->setShared(
    'fs',
    function (): Filesystem {
        $adapter = new LocalFilesystemAdapter(ROOT_DISK);
        return new Filesystem($adapter);
    }
);
/**
 * Database connection is created based in the parameters defined in the
 * configuration file
 */
$di->setShared(
    'db',
    function (): Mysql {
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
    function (): Cache {
        $serializerFactory = new SerializerFactory();
        $adapter = new RedisAdapter($serializerFactory, [
            'host' => 'redis',
            'port' => 6379,
            'lifetime' => 86400
        ]);

        return new Cache($adapter);
    }
);

$di->setShared(
    'authService',
    fn(): AuthService => new  AuthService()
);
$di->setShared(
    'session',
    function (): Manager {

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
    function (): true {
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
    fn(): \SseEmitter => new SseEmitter(new PhpOutputWriter())
);
