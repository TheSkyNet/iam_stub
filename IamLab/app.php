<?php
/**
 * Local variables
 *
 * @var Micro $app
 */

use IamLab\Service\Auth;
use IamLab\Service\Filepond\FilepondApi;
use IamLab\Service\OAuth;
use IamLab\Service\PusherApi;
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

/*
 * Pusher API
 */
$app->get('/api/pusher/config', [(new PusherApi()), "configAction"]);
$app->post('/api/pusher/auth', [(new PusherApi()), "authAction"]);
$app->post('/api/pusher/trigger', [(new PusherApi()), "triggerAction"]);
$app->get('/api/pusher/channel-info', [(new PusherApi()), "channelInfoAction"]);
$app->get('/api/pusher/channels', [(new PusherApi()), "channelsAction"]);
$app->post('/api/pusher/webhook', [(new PusherApi()), "webhookAction"]);

/*
 * OAuth API
 */
$app->get('/api/oauth/providers', [(new OAuth()), "providersAction"]);
$app->get('/api/oauth/redirect', [(new OAuth()), "redirectAction"]);
$app->get('/api/oauth/callback', [(new OAuth()), "callbackAction"]);
$app->post('/api/oauth/unlink', [(new OAuth()), "unlinkAction"]);

// Authentication API endpoints
$app->post('/auth/logout', [(new Auth()), "logoutAction"]);
$app->post('/auth/login', [(new Auth()), "loginAction"]);
$app->post('/auth/register', [(new Auth()), "registerAction"]);
$app->post('/auth/forgot-password', [(new Auth()), "forgotPasswordAction"]);
$app->get('/auth/user', [(new Auth()), "userAction"]);
$app->post('/auth/change-password', [(new Auth()), "changePasswordAction"]);
$app->post('/auth/reset-password', [(new Auth()), "resetPasswordAction"]);
$app->post('/auth/verify-email', [(new Auth()), "verifyEmailAction"]);


// JWT-related endpoints
$app->post('/auth/refresh-token', [(new Auth()), "refreshTokenAction"]);
$app->post('/auth/generate-api-key', [(new Auth()), "generateApiKeyAction"]);
$app->get('/auth/profile', [(new Auth()), "profileAction"]);
$app->post('/auth/update-profile', [(new Auth()), "updateProfileAction"]);

// QR Code login endpoints
$app->post('/auth/generate-qr-code', [(new Auth()), "generateQRCodeAction"]);
$app->post('/auth/check-qr-status', [(new Auth()), "checkQRStatusAction"]);
$app->post('/auth/authenticate-qr', [(new Auth()), "authenticateQRAction"]);

// Reverse QR Code login endpoints (mobile generates QR, desktop scans)
$app->post('/auth/generate-mobile-qr-code', [(new Auth()), "generateMobileQRCodeAction"]);
$app->post('/auth/check-mobile-qr-status', [(new Auth()), "checkMobileQRStatusAction"]);
$app->post('/auth/authenticate-mobile-qr', [(new Auth()), "authenticateMobileQRAction"]);
/**
 * Not found handler
 */
$app->notFound(function () use ($app) {
    /**
     * Check if the request is AJAX or accepts a JSON response.
     */
    if ($app->request->isAjax() || str_contains($app->request->getHeader('Accept'), 'application/json')) {
        $app->response->setStatusCode(404, 'Not Found');
        $app->response->setContentType('application/json', 'UTF-8');
        $app->response->setJsonContent([
            'status' => 'error',
            'message' => 'The requested resource was not found.'
        ]);
        $app->response->send();
    } else {
        $app->response->setStatusCode(404, "Not Found")->sendHeaders();
        echo $app['view']->render('404');
    }
});