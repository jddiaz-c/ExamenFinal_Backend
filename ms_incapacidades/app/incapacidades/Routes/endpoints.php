<?php

use App\Incapacidades\Handlers\IncapacidadHandler;
use Slim\App;
use MsCore\Middlewares\AuthMiddleware;

return function (App $app) {
    $app->group('/incapacidades', function ($group) {
        $group->get('', [IncapacidadHandler::class, 'all']);
        $group->get('/buscar', [IncapacidadHandler::class, 'buscar']);
        $group->get('/{id}', [IncapacidadHandler::class, 'detail']);
        $group->post('', [IncapacidadHandler::class, 'create']);
        $group->put('/{id}', [IncapacidadHandler::class, 'update']);
        $group->patch('/{id}/estado', [IncapacidadHandler::class, 'cambiarEstado']);
        $group->patch('/{id}/finalizar', [IncapacidadHandler::class, 'finalizar']);
    })->add(new AuthMiddleware());
};