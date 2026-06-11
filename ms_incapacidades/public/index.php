<?php

use Slim\Factory\AppFactory;
use MsCore\Middlewares\CorsMiddleware;
use MsCore\Config\Database;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

Database::connect();

$routes = require __DIR__ . '/../app/Incapacidades/Routes/endpoints.php';

$app = AppFactory::create();

$app->options('/{routes:.+}', fn($req, $res) => $res);
$app->add(new CorsMiddleware());

$routes($app);

$app->run();