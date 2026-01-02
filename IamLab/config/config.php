<?php

use Phalcon\Config\Config;

defined('APP_PATH') || define('APP_PATH', realpath('./IamLab'));
defined('TMP_PATH') || define('TMP_PATH', sys_get_temp_dir());
defined('TMP_DISK') || define('TMP_DISK', '/var/www/html/files/tmp');
defined('ROOT_DISK') || define('ROOT_DISK', '/');
defined('FILE_PATH') || define('FILE_PATH', '/var/www/html/public/files');

return new Config([
    'app' => [
        'encryption_key' => App\Core\Helpers\env('APP_KEY', 'def00000c6bbb616abfcd4abb5664b54c8002f6884d74e9b9778caab38c70c84f87a2c697ae66c9efb625ed18333c372e49311c575f2367ab484b96aa9ca5bb3d81cf4a1'),
        'baseUri' => App\Core\Helpers\env('APP_BASE_URI'),
        'env' => App\Core\Helpers\env('APP_ENV'),
        'name' => App\Core\Helpers\env('APP_NAME'),
        'timezone' => App\Core\Helpers\env('APP_TIMEZONE'),
        'url' => App\Core\Helpers\env('APP_URL'),
        'version' => App\Core\Helpers\env('VERSION'),
        'time' => microtime(true),
    ],
    'database' => [
        'adapter' => 'Mysql',
        'host' => App\Core\Helpers\env('DB_HOST', 'mysql'),
        'username' => App\Core\Helpers\env('DB_USERNAME', 'phalcons'),
        'password' => App\Core\Helpers\env('DB_PASSWORD', 'phalcons'),
        'dbname' => App\Core\Helpers\env('DB_NAME', 'phalcons'),
    ],

    'application' => [
        'modelsDir' => APP_PATH . '/Model/',
        'controllersDir' => APP_PATH . '/Service/',
        'migrationsDir' => APP_PATH . '/Migrations/',
        'viewsDir' => APP_PATH . '/views/',
        'baseUri' => '/',
    ],
    'redis' => [
        'host' => App\Core\Helpers\env('REDIS_HOST', 'redis'),
        'port' => App\Core\Helpers\env('REDIS_PORT', 6379),
        'password' => App\Core\Helpers\env('REDIS_PASSWORD', ''),
        'timeout' => App\Core\Helpers\env('REDIS_TIMEOUT', 0),
        'persistent' => App\Core\Helpers\env('REDIS_PERSISTENT', false),
        'database' => App\Core\Helpers\env('REDIS_DATABASE', 0),
        'prefix' => App\Core\Helpers\env('REDIS_PREFIX', ''),

        'lifetime' => App\Core\Helpers\env('REDIS_LIFETIME', 0),
        'retryInterval' => App\Core\Helpers\env('REDIS_RETRYINTERVAL', 0),
        'maxTries' => App\Core\Helpers\env('REDIS_MAXTRIES', 0),
        'usePipeline' => App\Core\Helpers\env('REDIS_USEPIPELINE', false),
        'useCluster' => App\Core\Helpers\env('REDIS_USECLUSTER', false),
        'clusterNodes' => App\Core\Helpers\env('REDIS_CLUSTERNODES', ''),
        'failover' => App\Core\Helpers\env('REDIS_FAILOVER', false),
        'maxRedirects' => App\Core\Helpers\env('REDIS_MAXREDIRECTS', 0),
        'readOnly' => App\Core\Helpers\env('REDIS_READONLY', false),
    ],

    'session' => [
        'adapter' => 'Files',
        'lifetime' => 3600,
        'path' => '/tmp',
        'domain' => null,
        'secure' => false,
        'httponly' => true,
        'name' => 'PHPSESSID',
        'autorefresh' => true,
        'savePath' => null,
    ],

    'logger' => [
        'enabled' => App\Core\Helpers\env('LOG_ENABLED', true),
        'level' => App\Core\Helpers\env('LOG_LEVEL', 'debug'),
        'path' => App\Core\Helpers\env('LOG_PATH', '/var/www/html/files/logs/app.log'),
        'format' => App\Core\Helpers\env('LOG_FORMAT', '[%date%][%level%] %message%'),
    ],
    'filepond' => [
        'temp_folder' => App\Core\Helpers\env('FILEPOND_TEMP_FOLDER', '/tmp'),
        'temp_disk' => App\Core\Helpers\env('FILEPOND_TEMP_DISK', 'local'),
        'disk' => App\Core\Helpers\env('FILEPOND_DISK', 'local'),
        'validation_rules' => [],

    ],

    'email' => [
        'provider' => App\Core\Helpers\env('MAIL_PROVIDER', 'mailhog'),
        'from_email' => App\Core\Helpers\env('MAIL_FROM_EMAIL', 'noreply@example.com'),
        'from_name' => App\Core\Helpers\env('MAIL_FROM_NAME', 'Phalcon Stub'),

        'mailhog' => [
            'host' => App\Core\Helpers\env('MAILHOG_HOST', 'mailhog'),
            'port' => App\Core\Helpers\env('MAILHOG_PORT', 1025),
            'username' => App\Core\Helpers\env('MAILHOG_USERNAME', ''),
            'password' => App\Core\Helpers\env('MAILHOG_PASSWORD', ''),
            'encryption' => App\Core\Helpers\env('MAILHOG_ENCRYPTION', ''),
        ],

        'resend' => [
            'api_key' => App\Core\Helpers\env('RESEND_API_KEY', ''),
            'endpoint' => App\Core\Helpers\env('RESEND_ENDPOINT', 'https://api.resend.com'),
        ],
    ],

    'pusher' => [
        'app_id' => App\Core\Helpers\env('PUSHER_APP_ID', ''),
        'key' => App\Core\Helpers\env('PUSHER_APP_KEY', ''),
        'secret' => App\Core\Helpers\env('PUSHER_APP_SECRET', ''),
        'cluster' => App\Core\Helpers\env('PUSHER_APP_CLUSTER', 'mt1'),
        'use_tls' => App\Core\Helpers\env('PUSHER_USE_TLS', true),
        'host' => App\Core\Helpers\env('PUSHER_HOST', null),
        'port' => App\Core\Helpers\env('PUSHER_PORT', null),
        'scheme' => App\Core\Helpers\env('PUSHER_SCHEME', 'https'),
        'verify_ssl' => App\Core\Helpers\env('PUSHER_VERIFY_SSL', true),
        'disable_stats' => App\Core\Helpers\env('PUSHER_DISABLE_STATS', false),
        'enabled_transports' => ['ws', 'wss'],
    ],

    'jwt' => [
        'secret' => App\Core\Helpers\env('JWT_SECRET', 'your-secret-key-change-this-in-production'),
        'algorithm' => App\Core\Helpers\env('JWT_ALGORITHM', 'HS256'),
        'access_token_expiry' => App\Core\Helpers\env('JWT_ACCESS_TOKEN_EXPIRY', 3600), // 1 hour
        'refresh_token_expiry' => App\Core\Helpers\env('JWT_REFRESH_TOKEN_EXPIRY', 604800), // 7 days
        'issuer' => App\Core\Helpers\env('JWT_ISSUER', 'phalcon-stub'),
        'audience' => App\Core\Helpers\env('JWT_AUDIENCE', 'phalcon-stub-users'),
    ],

    'oauth' => [
        'enabled' => App\Core\Helpers\env('OAUTH_ENABLED', false),
        'redirect_uri' => App\Core\Helpers\env('OAUTH_REDIRECT_URI', '/auth/oauth/callback'),

        'google' => [
            'enabled' => App\Core\Helpers\env('OAUTH_GOOGLE_ENABLED', false),
            'client_id' => App\Core\Helpers\env('OAUTH_GOOGLE_CLIENT_ID', ''),
            'client_secret' => App\Core\Helpers\env('OAUTH_GOOGLE_CLIENT_SECRET', ''),
            'redirect_uri' => App\Core\Helpers\env('OAUTH_GOOGLE_REDIRECT_URI', '/auth/oauth/google/callback'),
            'scopes' => ['openid', 'profile', 'email'],
        ],

        'github' => [
            'enabled' => App\Core\Helpers\env('OAUTH_GITHUB_ENABLED', false),
            'client_id' => App\Core\Helpers\env('OAUTH_GITHUB_CLIENT_ID', ''),
            'client_secret' => App\Core\Helpers\env('OAUTH_GITHUB_CLIENT_SECRET', ''),
            'redirect_uri' => App\Core\Helpers\env('OAUTH_GITHUB_REDIRECT_URI', '/auth/oauth/github/callback'),
            'scopes' => ['user:email'],
        ],

        'facebook' => [
            'enabled' => App\Core\Helpers\env('OAUTH_FACEBOOK_ENABLED', false),
            'client_id' => App\Core\Helpers\env('OAUTH_FACEBOOK_CLIENT_ID', ''),
            'client_secret' => App\Core\Helpers\env('OAUTH_FACEBOOK_CLIENT_SECRET', ''),
            'redirect_uri' => App\Core\Helpers\env('OAUTH_FACEBOOK_REDIRECT_URI', '/auth/oauth/facebook/callback'),
            'scopes' => ['email', 'public_profile'],
        ],

        'generic' => [
            'enabled' => App\Core\Helpers\env('OAUTH_GENERIC_ENABLED', false),
            'client_id' => App\Core\Helpers\env('OAUTH_GENERIC_CLIENT_ID', ''),
            'client_secret' => App\Core\Helpers\env('OAUTH_GENERIC_CLIENT_SECRET', ''),
            'redirect_uri' => App\Core\Helpers\env('OAUTH_GENERIC_REDIRECT_URI', '/auth/oauth/generic/callback'),
            'authorization_url' => App\Core\Helpers\env('OAUTH_GENERIC_AUTHORIZATION_URL', ''),
            'token_url' => App\Core\Helpers\env('OAUTH_GENERIC_TOKEN_URL', ''),
            'user_info_url' => App\Core\Helpers\env('OAUTH_GENERIC_USER_INFO_URL', ''),
            'scopes' => explode(',', App\Core\Helpers\env('OAUTH_GENERIC_SCOPES', 'openid,profile,email')),
        ],
    ],

]);
