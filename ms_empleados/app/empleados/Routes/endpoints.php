<?php

use App\Empleados\Handlers\EmpleadoHandler;
use Slim\App;

require_once __DIR__ . '/../../Middlewares/AuthMiddleware.php';

return function (App $app) {
    $app->group('', function ($group) {
        $group->get('/empleados', [EmpleadoHandler::class, 'all']);
        $group->get('/empleados/buscar', [EmpleadoHandler::class, 'buscar']);
        $group->get('/empleados/{id}', [EmpleadoHandler::class, 'detail']);
        $group->post('/empleados', [EmpleadoHandler::class, 'create']);
        $group->put('/empleados/{id}', [EmpleadoHandler::class, 'update']);
        $group->patch('/empleados/{id}/estado', [EmpleadoHandler::class, 'cambiarEstado']);
    })->add(new AuthMiddleware());
};