<?php

/**
 * @OA\Info(
 *   title="Eldorado tc REST API",
 *   version="1.0.0",
 *   contact={
 *     "email": "dmitriy.novotorov@gmail.com"
 *   }
 * )
 * @OA\SecurityScheme(
 *         securityScheme="api_key",
 *         type="apiKey",
 *         name="X-API-Key",
 *         in="header"
 *     ),
 *
 */



/**
 * @OA\Schema(
 *   schema="Success Response",
 *   @OA\Property(
 *     property="code",
 *     type="string"
 * ),
 *     @OA\Property(
 *     property="message",
 *     type="string"
 * ),
 *     @OA\Property(
 *     property="data",
 * )
 * )
 */



use Phalcon\Mvc\Micro\Collection;

$collection = new Collection();
$collection->setHandler('\App\Controllers\SongController', true);
$collection->setPrefix('/songs');
$collection->get('/', 'get');
$collection->post('/', 'post');
$collection->put('/{id}', 'put');
$collection->delete('/{id}', 'delete');
$app->mount($collection);


$collection = new Collection();
$collection->setHandler('\App\Controllers\AdminController', true);
$collection->setPrefix('/admin');
$collection->get('/profile', 'profile', 'adminUserProfile');
$collection->post('/users', 'signUp', 'createAdminUser');
$collection->get('/users', 'getUsers', 'getAdminUsers');
$collection->post('/login', 'signIn', 'adminUserLogin');
$collection->post('/roles', 'createRole', 'createRole');
$collection->get('/roles', 'getRoles', 'getRoles');
$collection->get('/permissions', 'getRolesPermissions', 'getPermissions');
$collection->get('/roles/{id}', 'getRoles', 'getRole');
$collection->get('/roles/{id}/permissions', 'getRolesPermissions', 'getRolePermissions');
$collection->put('/roles/{id}/permissions', 'putRolesPermissions', 'putRolesPermissions');

$app->mount($collection);

// not found URLs
$app->notFound(
    function () use ($app) {
        $app->response->setStatusCode(404, "Not Found")->sendHeaders();
        $app->response->setContentType('application/json', 'UTF-8');
        $app->response->setJsonContent(array(
            "code" => 404,
            "status" => "error",
            "messages" => "URL Not found",
        ));
    }
);