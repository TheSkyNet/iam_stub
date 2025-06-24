<?php
/**
 * Local variables
 *
 * @var Micro $app
 */

use IamLab\Service\Admin;
use IamLab\Service\API;
use IamLab\Service\Auth;
use IamLab\Service\Filepond\FilepondApi;
use Phalcon\Mvc\Micro;
use IamLab\Service\SettingsService;
/**
 * Add your routes here
 */
$app->get(
    '/',
    function () use ($app) {
        $settingsService = new SettingsService();
        $settingsService->initialize();

        echo $app['view']->render('index',[
            'settings' => $settingsService->getFormatted(),
//            'rawSettings' => $settingsService->getRaw(),
        ]);
    }
);

/*
 * API
 */
$app->get('/api/v1/all', [(new API), "getAllAction"]);

$app->get('/api/v1/post', [(new API), "getPostsAction"]);
$app->get('/api/v1/post/{id}', [(new API), "getPostsByIdAction"]);
$app->post('/api/v1/post', [(new API), "newPostAction"]);
$app->put('/api/v1/post/{id}', [(new API), "updatePostsAction"]);
$app->delete('/api/v1/post/{id}', [(new API), "deletePostsAction"]) ;

$app->get('/api/v1/user', [(new API), "getUsersAction"]);
$app->get('/api/v1/user/{id}', [(new API), "getUserByIdAction"]);
$app->post('/api/v1/user', [(new API), "newUserAction"]);
$app->put('/api/v1/user/{id}', [(new API), "updateUserAction"]);
$app->delete('/api/v1/user/{id}', [(new API), "deleteUserAction"]) ;

$app->get('/api/v1/package', [(new API), "getPackageAction"]);
$app->get('/api/v1/package/{id}', [(new API), "getPackageByIdAction"]);
$app->post('/api/v1/package', [(new API), "newPackageAction"]);
$app->put('/api/v1/package/{id}', [(new API), "updatePackageAction"]);
$app->delete('/api/v1/package/{id}', [(new API), "deletePackageAction"]) ;


$app->get('/api/v1/project', [(new API), "getProjectAction"]);
$app->get('/api/v1/project/{id}', [(new API), "getProjectByIdAction"]);
$app->post('/api/v1/project', [(new API), "newProjectAction"]);
$app->put('/api/v1/project/{id}', [(new API), "updateProjectAction"]);
$app->delete('/api/v1/project/{id}', [(new API), "deleteProjectAction"]) ;

$app->get('/api/v1/code', [(new API), "getCodeAction"]);
$app->get('/api/v1/code/{id}', [(new API), "getCodeByIdAction"]);
$app->get('/api/v1/code/{id}/html', [(new API), "getCodeByIdHtmlAction"]);
$app->post('/api/v1/code', [(new API), "newCodeAction"]);
$app->put('/api/v1/code/{id}', [(new API), "updateCodeAction"]);
$app->delete('/api/v1/code/{id}', [(new API), "deleteCodeAction"]) ;

$app->get('/api/v1/file', [(new API()), "getFilesAction"]);
$app->post('/api/v1/file/save', [(new API()), "saveFilesAction"]);
$app->post('/api/v1/file', [(new FilepondApi()), "process"]);
$app->patch('/api/v1/file', [(new FilepondApi()), "patch"]);
//$app->head('/api/v1/file', [(new FilepondApi()), "head"]);
$app->post('/api/v1/seo/generate/{type}', [(new API()), "generateSeoFileAction"]);


$app->get('/api/v1/settings', [(new API), "getSettingsAction"]);
$app->get('/api/v1/settings/{key}', [(new API), "getSettingByKeyAction"]);
$app->post('/api/v1/settings', [(new API), "newSettingAction"]);
$app->put('/api/v1/settings/{key}', [(new API), "updateSettingAction"]);
$app->delete('/api/v1/settings/{key}', [(new API), "deleteSettingAction"]);


/*
 * Admin
 */
$app->get('/admin', [(new Admin()), "indexAction"]);

/*
 *
 */

$app->get(
  '/auth',
  function () use ($app)
  {
    echo $app['view']->render('auth');
  }
);
$app->post('/auth/logout', [(new Auth()), "logoutAction"]);
$app->post('/auth', [(new Auth()), "authAction"]);
$app->get('/auth', [(new Auth()), "userAction"]);
/**
 * Not found handler
 */
$app->notFound(
  function () use ($app)
  {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo $app['view']->render('404');
  }
);

