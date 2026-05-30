<?php
use Slim\Routing\RouteCollectorProxy;
use App\Auth\Handlers\AuthHandler;
use Slim\App;

return function (App $app) {
    $app->group('/auth', function (RouteCollectorProxy $group) {
        $group->post('/login', [AuthHandler::class, 'all']);
        $group->post('/logout', [AuthHandler::class, 'detail']);
        $group->post('/validate', [AuthHandler::class, 'create']);
    });
};
