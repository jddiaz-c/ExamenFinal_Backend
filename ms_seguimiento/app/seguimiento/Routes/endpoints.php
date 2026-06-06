<?php

use App\Seguimiento\Handlers\SeguimientoHandler;
use Slim\App;
use MsCore\Middlewares\AuthMiddleware;

return function (App $app) {
    $app->group('/seguimientos', function ($group) {
        $group->get('', [SeguimientoHandler::class, 'all']);
        $group->get('/buscar', [SeguimientoHandler::class, 'buscar']);
        $group->get('/{id}', [SeguimientoHandler::class, 'detail']);
        $group->post('', [SeguimientoHandler::class, 'create']);
    })->add(new AuthMiddleware());
};