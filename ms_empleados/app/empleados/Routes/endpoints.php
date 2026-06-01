<?php

use App\Empleados\Handlers\EmpleadoHandler;
use Slim\App;
use MsCore\Middlewares\AuthMiddleware;

return function (App $app) {
    $app->group('/empleados', function ($group) {
        $group->get('', [EmpleadoHandler::class, 'all']);
        $group->get('/buscar', [EmpleadoHandler::class, 'buscar']);
        $group->get('/{id}', [EmpleadoHandler::class, 'detail']);
        $group->post('', [EmpleadoHandler::class, 'create']);
        $group->put('/{id}', [EmpleadoHandler::class, 'update']);
        $group->patch('/{id}/estado', [EmpleadoHandler::class, 'cambiarEstado']);
    })->add(new AuthMiddleware());
};