<?php
use Slim\Routing\RouteCollectorProxy;
use App\Auth\Handlers\AuthHandler;
use Slim\App;

return function (App $app) {
    $app->group('/auth', function (RouteCollectorProxy $group) {
        $group->post('/login', [AuthHandler::class, 'login']);
        $group->post('/logout', [AuthHandler::class, 'logout']);
        $group->post('/validate', [AuthHandler::class, 'validate']);
    });
};
