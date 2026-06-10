<?php
use Slim\Routing\RouteCollectorProxy;
use App\Auth\Handlers\AuthHandler;
use App\Auth\Handlers\UsuariosHandler;
require_once __DIR__ . '/../Middlewares/AdminMiddleware.php';
use Slim\App;

return function (App $app) {
    $app->group('/auth', function (RouteCollectorProxy $group) {
        $group->post('/login', [AuthHandler::class, 'login']);
        $group->post('/logout', [AuthHandler::class, 'logout']);
        $group->post('/validate', [AuthHandler::class, 'validate']);
    });

    $app->group('/usuarios', function (RouteCollectorProxy $group) {
        $group->get('', [UsuariosHandler::class, 'all']);
        $group->get('/{id}', [UsuariosHandler::class, 'detail']);
        $group->post('', [UsuariosHandler::class, 'create']);
        $group->put('/{id}', [UsuariosHandler::class, 'update']);
        $group->delete('/{id}', [UsuariosHandler::class, 'delete']);
        $group->patch('/{id}/estado', [UsuariosHandler::class, 'cambiarEstado']);

    })->add(new AdminMiddleware());
};
