<?php
/**
 * Local variables
 *
 * @var Micro $app
 */

use IamLab\Core\Routing\RouteGroup;
use IamLab\Service\Auth;
use IamLab\Service\Filepond\FilepondApi;
use IamLab\Service\OAuth;
use IamLab\Service\PusherApi;
use IamLab\Service\RolesApi;
use IamLab\Service\UsersApi;
use IamLab\Service\JobsApi;
use IamLab\Service\ErrorsApi;
use IamLab\Service\SseApi;
use IamLab\Service\SettingsService;
use Phalcon\Mvc\Micro;

/**
 * Route Groups - Organized routing with guards
 */

// =============================================================================
// PUBLIC ROUTES (No authentication required)
// =============================================================================
RouteGroup::create($app)
    ->group(function ($group) {
        // Home page
        $group->get('/', function ($app) {
            $settingsService = new SettingsService();
            $settingsService->initialize();
            echo $app['view']->render('index', ['settings' => $settingsService->getFormatted()]);
        });
    });

// =============================================================================
// PUBLIC API ROUTES (No authentication required)
// =============================================================================
RouteGroup::create($app, '/api')
    ->group(function ($group) {
        // File upload API
        $group->post('/v1/file', [(new FilepondApi()), "process"]);
        $group->patch('/v1/file', [(new FilepondApi()), "patch"]);
        
        // OAuth API (public endpoints)
        $group->get('/oauth/providers', [(new OAuth()), "providersAction"]);
        $group->get('/oauth/redirect', [(new OAuth()), "redirectAction"]);
        $group->get('/oauth/callback', [(new OAuth()), "callbackAction"]);

        // Pusher public endpoints
        $group->get('/pusher/config', [(new PusherApi()), "configAction"]);
        $group->post('/pusher/webhook', [(new PusherApi()), "webhookAction"]);

        // Errors public endpoint (frontend/client can report)
        $group->post('/errors', [(new ErrorsApi()), "createAction"]);

        // Server-Sent Events (SSE) public demo endpoints
        $group->get('/sse/clock', [(new SseApi()), "clockAction"]);
        $group->get('/sse/echo', [(new SseApi()), "echoAction"]);
        $group->get('/sse/test', [(new SseApi()), "testAction"]);
    });

// =============================================================================
// AUTHENTICATED API ROUTES (Authentication required)
// =============================================================================
RouteGroup::create($app, '/api')
    ->requireAuth()
    ->group(function ($group) {
        // Pusher authenticated endpoints
        $group->post('/pusher/auth', [(new PusherApi()), "authAction"]);
        $group->post('/pusher/trigger', [(new PusherApi()), "triggerAction"]);
        $group->get('/pusher/channel-info', [(new PusherApi()), "channelInfoAction"]);
        $group->get('/pusher/channels', [(new PusherApi()), "channelsAction"]);
        
        // OAuth authenticated endpoints
        $group->post('/oauth/unlink', [(new OAuth()), "unlinkAction"]);
        
        // Jobs API endpoints
        $group->get('/jobs', [(new JobsApi()), "indexAction"]);
        $group->get('/jobs/stats', [(new JobsApi()), "statsAction"]);
        $group->get('/jobs/types', [(new JobsApi()), "typesAction"]);
        $group->get('/jobs/{id}', [(new JobsApi()), "showAction"]);
        $group->post('/jobs', [(new JobsApi()), "createAction"]);
        $group->post('/jobs/cleanup', [(new JobsApi()), "cleanupAction"]);
        $group->post('/jobs/bulk', [(new JobsApi()), "bulkAction"]);
        $group->post('/jobs/{id}/retry', [(new JobsApi()), "retryAction"]);
        $group->delete('/jobs/{id}', [(new JobsApi()), "deleteAction"]);
    });

// =============================================================================
// ADMIN API ROUTES (Admin role required)
// =============================================================================
RouteGroup::create($app, '/api')
    ->requireAdmin()
    ->group(function ($group) {
        // Role management API
        $group->get('/roles', [(new RolesApi()), "indexAction"]);
        $group->get('/roles/{id}', [(new RolesApi()), "showAction"]);
        $group->post('/roles', [(new RolesApi()), "createAction"]);
        $group->put('/roles/{id}', [(new RolesApi()), "updateAction"]);
        $group->delete('/roles/{id}', [(new RolesApi()), "deleteAction"]);
        $group->get('/roles/search', [(new RolesApi()), "searchAction"]);
        
        // User management API
        $group->get('/users', [(new UsersApi()), "indexAction"]);
        $group->get('/users/{id}', [(new UsersApi()), "showAction"]);
        $group->post('/users', [(new UsersApi()), "createAction"]);
        $group->put('/users/{id}', [(new UsersApi()), "updateAction"]);
        $group->delete('/users/{id}', [(new UsersApi()), "deleteAction"]);
        $group->get('/users/search', [(new UsersApi()), "searchAction"]);

        // Errors management endpoints
        $group->get('/errors', [(new ErrorsApi()), "indexAction"]);
        $group->get('/errors/{id}', [(new ErrorsApi()), "showAction"]);
        $group->delete('/errors/{id}', [(new ErrorsApi()), "deleteAction"]);
        $group->post('/errors/cleanup', [(new ErrorsApi()), "cleanupAction"]);
    });

// =============================================================================
// PUBLIC AUTH ROUTES (No authentication required)
// =============================================================================
RouteGroup::create($app, '/auth')
    ->group(function ($group) {
        // Public authentication endpoints
        $group->post('/login', [(new Auth()), "loginAction"]);
        $group->post('/register', [(new Auth()), "registerAction"]);
        $group->post('/forgot-password', [(new Auth()), "forgotPasswordAction"]);
        $group->post('/reset-password', [(new Auth()), "resetPasswordAction"]);
        $group->post('/verify-email', [(new Auth()), "verifyEmailAction"]);
        $group->post('/refresh-token', [(new Auth()), "refreshTokenAction"]);
        // Frontend auth behavior config (backend-controlled)
        $group->get('/config', [(new Auth()), "configAction"]);
        
        // QR Code public endpoints
        $group->post('/generate-qr-code', [(new Auth()), "generateQRCodeAction"]);
        $group->post('/check-qr-status', [(new Auth()), "checkQRStatusAction"]);
        $group->post('/generate-mobile-qr-code', [(new Auth()), "generateMobileQRCodeAction"]);
        $group->post('/check-mobile-qr-status', [(new Auth()), "checkMobileQRStatusAction"]);
    });

// =============================================================================
// PROTECTED AUTH ROUTES (Authentication required)
// =============================================================================
RouteGroup::create($app, '/auth')
    ->requireAuth()
    ->group(function ($group) {
        // Authenticated user endpoints
        $group->post('/logout', [(new Auth()), "logoutAction"]);
        $group->get('/user', [(new Auth()), "userAction"]);
        $group->post('/change-password', [(new Auth()), "changePasswordAction"]);
        $group->post('/generate-api-key', [(new Auth()), "generateApiKeyAction"]);
        $group->get('/profile', [(new Auth()), "profileAction"]);
        $group->post('/update-profile', [(new Auth()), "updateProfileAction"]);
        
        // QR Code authenticated endpoints
        $group->post('/authenticate-qr', [(new Auth()), "authenticateQRAction"]);
        $group->post('/authenticate-mobile-qr', [(new Auth()), "authenticateMobileQRAction"]);
    });
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