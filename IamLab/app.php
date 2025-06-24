<?php
/**
 * Local variables
 *
 * @var Micro $app
 */

use IamLab\Service\Auth;
use IamLab\Service\Filepond\FilepondApi;
use IamLab\Service\SettingsService;
use Phalcon\Mvc\Micro;

/**
 * Add your routes here
 */
$app->get('/', function () use ($app) {
    $settingsService = new SettingsService();
    $settingsService->initialize();

    echo $app['view']->render('index', ['settings' => $settingsService->getFormatted(),
    ]);
});
$app->get('/admin', function () use ($app) {
    $settingsService = new SettingsService();
    $settingsService->initialize();

    echo $app['view']->render('admin', ['settings' => $settingsService->getFormatted(),
    ]);
});

/*
 * API
 */
$app->post('/api/v1/file', [(new FilepondApi()), "process"]);
$app->patch('/api/v1/file', [(new FilepondApi()), "patch"]);


$app->get('/auth', function () use ($app) {
    echo $app['view']->render('auth');
});
$app->post('/auth/logout', [(new Auth()), "logoutAction"]);
$app->post('/auth', [(new Auth()), "authAction"]);
$app->get('/auth', [(new Auth()), "userAction"]);
/**
 * Not found handler
 */
$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo $app['view']->render('404');
});

