<?php

namespace App\Core\Helpers;
use IamLab\Core\Console\Table\Table;
use IamLab\Core\Email\EmailService;
use IamLab\Core\Env\Env;
use JetBrains\PhpStorm\NoReturn;
use Phalcon\Di\FactoryDefault;
use Exception;

/** @var FactoryDefault $di */

/**
 * Get a value from the configuration.
 *
 * @param string $key The key to retrieve, using dot notation for nested values.
 * @param mixed|null $default The default value to return if the key is not found.
 * @return mixed The configuration value.
 */
function config($key, $default = null)
{

    $config = di('config')->path($key);

    if ($config === null) {
        return $default;
    }
    if (is_array($config) || is_iterable($config)) {
        return $config->toArray();
    }
    return $config;
}

/**
 * Get a service from the dependency injection container.
 *
 * @param string $service The name of the service to retrieve.
 * @return mixed The service instance.
 */
function di(string $service): mixed
{

    global $di;
    return $di->get($service);
}

/**
 * Get an environment variable.
 *
 * @param string $key The environment variable key.
 * @param mixed|null $default The default value if the environment variable is not set.
 * @return mixed The value of the environment variable.
 */
function env(string $key, mixed $default = null): mixed
{
    return getenv($key) ?: $default;
}

/**
 * Move a file from one location to another on a specified disk.
 *
 * @param string $disk The filesystem disk service name from the DI container.
 * @param string $from The source path.
 * @param string $to The destination path.
 * @return bool True on success, false on failure.
 */
function moveTo(string $disk, string $from, string $to): bool
{
    return di($disk)->move($from, $to);
}

/**
 * Dump the passed variables and end the script.
 *
 * @param mixed ...$variable The variables to dump.
 */
#[NoReturn] function dd(...$variable): void
{
    // if it is cli lets just dump it out
    if (php_sapi_name() === 'cli') {
        foreach ($variable as $var){
            var_dump($var);
        }
        die();
    }
    echo "<pre>";
    foreach ($variable as $var){
        var_dump($var);
    }
    echo "</pre>";
    die();
}

/**
 * Load environment variables from a .env file.
 *
 * @param string $path The path to the .env file.
 */
function loadEnv(string $path =''): void
{
    (new Env($path))->load();
}

/**
 * Send an email.
 *
 * @param string $to The recipient's email address.
 * @param string $subject The subject of the email.
 * @param string $body The body of the email.
 * @param array $options Additional options for the email (e.g., attachments, CC, BCC).
 * @return bool True if the email was sent successfully, false otherwise.
 */
function email(string $to, string $subject, string $body, array $options = []): bool
{
    try {
        // Get the Email Service from DI container
        $emailService = new EmailService();

        // Send the email using the service
        return $emailService->send($to, $subject, $body, $options);
    } catch (Exception $e) {
        // Log error if needed and return false
        error_log("Email helper error: " . $e->getMessage());
        return false;
    }
}

/**
 * Render a console table.
 *
 * @param array $data The data to display in the table.
 * @param string|null $title The title of the table.
 */
function table(array $data, string $title = null): void
{
    $table = new Table();
    $table->setData($data)
        ->setTitle($title)
        ->render();
}