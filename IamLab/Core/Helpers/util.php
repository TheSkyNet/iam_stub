<?php

namespace App\Core\Helpers;
use IamLab\Core\Env\Env;
use Phalcon\Di\FactoryDefault;
use Exception;

/** @var FactoryDefault $di */

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

function di($service)
{

    global $di;
    return $di->get($service);
}

function env($key, $default = null)
{
    return getenv($key) ?: $default;
}

function moveTo(string $disk, string $from, string $to)
{
    return di($disk)->move($from, $to);
}

function dd(...$variable)
{
    echo '<pre>';
    die(var_dump($variable));
    echo '</pre>';
}

function loadEnv($path =''){
      (new Env($path))->load();
}

function email(string $to, string $subject, string $body, array $options = []): bool
{
    try {
        // Get the Email Service from DI container
        $emailService = new \IamLab\Core\Email\EmailService();

        // Send the email using the service
        return $emailService->send($to, $subject, $body, $options);
    } catch (Exception $e) {
        // Log error if needed and return false
        error_log("Email helper error: " . $e->getMessage());
        return false;
    }
}

function table(array $data, string $title = null): void
{
    $table = new \IamLab\Core\Console\Table\Table();
    $table->setData($data)
          ->setTitle($title)
          ->render();
}
