<?php

namespace App\Core\Helpers;
use IamLab\Core\Env\Env;
use Phalcon\Di\FactoryDefault;

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

    //return di($disk)->listContents($from);
    // dd(di($disk)->listContents('/tmp')->toArray(), $from);
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