<?php

use Phalcon\Config\Config;

use function IamLab\Core\Helpers\env;

defined('APP_PATH') || define('APP_PATH', realpath('./IamLab'));
defined('TMP_PATH') || define('TMP_PATH', sys_get_temp_dir());
defined('TMP_DISK') || define('TMP_DISK', '/var/www/html/files/tmp');
defined('ROOT_DISK') || define('ROOT_DISK', '/');
defined('FILE_PATH') || define('FILE_PATH', '/var/www/html/public/files');

return new Config([
    'app' => [
        'encryption_key' => env('APP_KEY', 'def00000c6bbb616abfcd4abb5664b54c8002f6884d74e9b9778caab38c70c84f87a2c697ae66c9efb625ed18333c372e49311c575f2367ab484b96aa9ca5bb3d81cf4a1'),
        'baseUri' => env('APP_BASE_URI'),
        'env' => env('APP_ENV'),
        'name' => env('APP_NAME'),
        'timezone' => env('APP_TIMEZONE'),
        'url' => env('APP_URL'),
        'version' => env('VERSION'),
        'time' => microtime(true),
    ],
    'database' => [
        'adapter' => 'Mysql',
        'host' => env('DB_HOST', 'mysql'),
        'username' => env('DB_USERNAME', 'phalcons'),
        'password' => env('DB_PASSWORD', 'phalcons'),
        'dbname' => env('DB_NAME', 'phalcons'),
    ],

    'application' => [
        'modelsDir' => APP_PATH . '/Model/',
        'controllersDir' => APP_PATH . '/Service/',
        'migrationsDir' => APP_PATH . '/Migrations/',
        'viewsDir' => APP_PATH . '/views/',
        'baseUri' => '/',
    ],
    'redis' => [
        'host' => env('REDIS_HOST', 'redis'),
        'port' => env('REDIS_PORT', 6379),
        'password' => env('REDIS_PASSWORD', ''),
        'timeout' => env('REDIS_TIMEOUT', 0),
        'persistent' => env('REDIS_PERSISTENT', false),
        'database' => env('REDIS_DATABASE', 0),
        'prefix' => env('REDIS_PREFIX', ''),

        'lifetime' => env('REDIS_LIFETIME', 0),
        'retryInterval' => env('REDIS_RETRYINTERVAL', 0),
        'maxTries' => env('REDIS_MAXTRIES', 0),
        'usePipeline' => env('REDIS_USEPIPELINE', false),
        'useCluster' => env('REDIS_USECLUSTER', false),
        'clusterNodes' => env('REDIS_CLUSTERNODES', ''),
        'failover' => env('REDIS_FAILOVER', false),
        'maxRedirects' => env('REDIS_MAXREDIRECTS', 0),
        'readOnly' => env('REDIS_READONLY', false),
    ],

    'cache' => [
        'default' => env('CACHE_DEFAULT', 'file'),
        'flush_all_by_default' => env('CACHE_FLUSH_ALL', true),
        'layers' => [
            'file' => [
                'adapter' => 'stream',
                'cacheDir' => env('CACHE_FILE_DIR', '/tmp/cache'),
                'lifetime' => 3600,
                'prefix' => 'iam_',
            ],
            'redis' => [
                'adapter' => 'redis',
                'host' => env('REDIS_HOST', 'redis'),
                'port' => env('REDIS_PORT', 6379),
                'index' => 1,
                'lifetime' => 3600,
                'prefix' => 'iam_',
            ],
            'apcu' => [
                'adapter' => 'apcu',
                'lifetime' => 3600,
                'prefix' => 'iam_',
            ],
            'memory' => [
                'adapter' => 'memory',
                'lifetime' => 3600,
                'prefix' => 'iam_',
            ],
        ],
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
        'enabled' => env('LOG_ENABLED', true),
        'level' => env('LOG_LEVEL', 'debug'),
        'path' => env('LOG_PATH', '/var/www/html/files/logs/app.log'),
        'format' => env('LOG_FORMAT', '[%date%][%level%] %message%'),
    ],
    'filepond' => [
        'temp_folder' => env('FILEPOND_TEMP_FOLDER', '/tmp'),
        'temp_disk' => env('FILEPOND_TEMP_DISK', 'local'),
        'disk' => env('FILEPOND_DISK', 'local'),
        'validation_rules' => [],

    ],

    'email' => [
        'provider' => env('MAIL_PROVIDER', 'mailhog'),
        'from_email' => env('MAIL_FROM_EMAIL', 'noreply@example.com'),
        'from_name' => env('MAIL_FROM_NAME', 'Phalcon Stub'),

        'mailhog' => [
            'host' => env('MAILHOG_HOST', 'mailhog'),
            'port' => env('MAILHOG_PORT', 1025),
            'username' => env('MAILHOG_USERNAME', ''),
            'password' => env('MAILHOG_PASSWORD', ''),
            'encryption' => env('MAILHOG_ENCRYPTION', ''),
        ],

        'resend' => [
            'api_key' => env('RESEND_API_KEY', ''),
            'endpoint' => env('RESEND_ENDPOINT', 'https://api.resend.com'),
        ],
    ],

    'pusher' => [
        'app_id' => env('PUSHER_APP_ID', ''),
        'key' => env('PUSHER_APP_KEY', ''),
        'secret' => env('PUSHER_APP_SECRET', ''),
        'cluster' => env('PUSHER_APP_CLUSTER', 'mt1'),
        'use_tls' => env('PUSHER_USE_TLS', true),
        'host' => env('PUSHER_HOST', null),
        'port' => env('PUSHER_PORT', null),
        'scheme' => env('PUSHER_SCHEME', 'https'),
        'verify_ssl' => env('PUSHER_VERIFY_SSL', true),
        'disable_stats' => env('PUSHER_DISABLE_STATS', false),
        'enabled_transports' => ['ws', 'wss'],
    ],

    'jwt' => [
        'secret' => env('JWT_SECRET', 'your-secret-key-change-this-in-production'),
        'algorithm' => env('JWT_ALGORITHM', 'HS256'),
        'access_token_expiry' => env('JWT_ACCESS_TOKEN_EXPIRY', 3600), // 1 hour
        'refresh_token_expiry' => env('JWT_REFRESH_TOKEN_EXPIRY', 604800), // 7 days
        'remember_me_access_token_expiry' => env('JWT_REMEMBER_ME_ACCESS_TOKEN_EXPIRY', 2592000), // 30 days
        'remember_me_refresh_token_expiry' => env('JWT_REMEMBER_ME_REFRESH_TOKEN_EXPIRY', 31536000), // 1 year
        'refresh_token_cookie' => env('JWT_REFRESH_TOKEN_COOKIE', 'refresh_token'),
        'cookie_domain' => env('COOKIE_DOMAIN', ''),
        'cookie_secure' => (bool)env('COOKIE_SECURE', false),
        'issuer' => env('JWT_ISSUER', 'phalcon-stub'),
        'audience' => env('JWT_AUDIENCE', 'phalcon-stub-users'),
    ],

    // Client-side auth behavior knobs, controlled by backend envs
    // Set AUTH_CLIENT_INACTIVITY_TIMEOUT_MINUTES to 0 or -1 to disable inactivity auto-logout
    'auth_client' => [
        'inactivity_timeout_minutes' => env('AUTH_CLIENT_INACTIVITY_TIMEOUT_MINUTES', 30),
        'token_check_interval_minutes' => env('AUTH_CLIENT_TOKEN_CHECK_INTERVAL_MINUTES', 5),
    ],

    'oauth' => [
        'enabled' => env('OAUTH_ENABLED', false),
        'redirect_uri' => env('OAUTH_REDIRECT_URI', '/auth/oauth/callback'),

        'google' => [
            'enabled' => env('OAUTH_GOOGLE_ENABLED', false),
            'client_id' => env('OAUTH_GOOGLE_CLIENT_ID', ''),
            'client_secret' => env('OAUTH_GOOGLE_CLIENT_SECRET', ''),
            'redirect_uri' => env('OAUTH_GOOGLE_REDIRECT_URI', '/auth/oauth/google/callback'),
            'scopes' => ['openid', 'profile', 'email'],
        ],

        'github' => [
            'enabled' => env('OAUTH_GITHUB_ENABLED', false),
            'client_id' => env('OAUTH_GITHUB_CLIENT_ID', ''),
            'client_secret' => env('OAUTH_GITHUB_CLIENT_SECRET', ''),
            'redirect_uri' => env('OAUTH_GITHUB_REDIRECT_URI', '/auth/oauth/github/callback'),
            'scopes' => ['user:email'],
        ],

        'facebook' => [
            'enabled' => env('OAUTH_FACEBOOK_ENABLED', false),
            'client_id' => env('OAUTH_FACEBOOK_CLIENT_ID', ''),
            'client_secret' => env('OAUTH_FACEBOOK_CLIENT_SECRET', ''),
            'redirect_uri' => env('OAUTH_FACEBOOK_REDIRECT_URI', '/auth/oauth/facebook/callback'),
            'scopes' => ['email', 'public_profile'],
        ],

        'generic' => [
            'enabled' => env('OAUTH_GENERIC_ENABLED', false),
            'client_id' => env('OAUTH_GENERIC_CLIENT_ID', ''),
            'client_secret' => env('OAUTH_GENERIC_CLIENT_SECRET', ''),
            'redirect_uri' => env('OAUTH_GENERIC_REDIRECT_URI', '/auth/oauth/generic/callback'),
            'authorization_url' => env('OAUTH_GENERIC_AUTHORIZATION_URL', ''),
            'token_url' => env('OAUTH_GENERIC_TOKEN_URL', ''),
            'user_info_url' => env('OAUTH_GENERIC_USER_INFO_URL', ''),
            'scopes' => explode(',', (string) env('OAUTH_GENERIC_SCOPES', 'openid,profile,email')),
        ],
    ],

]);
