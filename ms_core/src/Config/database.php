<?php
namespace MsCore\Config;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{
    public static function connect(): void
    {
        $capsule = new Capsule;

        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => $_ENV['DB_HOST'],
            'database'  => $_ENV['DB_NAME'],
            'username'  => $_ENV['DB_USER'],
            'password'  => $_ENV['DB_PASS'],
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        try {
            $capsule->getConnection()->getPdo();
        } catch (\PDOException $e) {
            http_response_code(503);
            header('Content-Type: application/json');
            echo json_encode([
                'error' => 'No se pudo conectar a la base de datos.'
            ]);
            exit;
        }
    }
}