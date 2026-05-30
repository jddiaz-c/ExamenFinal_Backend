<?php
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

require __DIR__ . '/../App/Config/Database.php';

$cors     = require __DIR__ . '/../App/Middlewares/CorsMiddleware.php';
$routes   = require __DIR__ . '/../App/Empleados/Routes/endpoints.php';

$app = AppFactory::create();

$cors($app);
$routes($app);

$app->run();