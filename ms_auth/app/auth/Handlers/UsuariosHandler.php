<?php

namespace App\Auth\Handlers;

use App\Auth\Controllers\UsuariosController;
use MsCore\Handlers\BaseHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;

class UsuariosHandler extends BaseHandler
{
    protected function getController(): UsuariosController
    {
        return new UsuariosController();
    }
    public function cambiarEstado(Request $req, Response $resp, array $args): Response
    {
        try {
            $data = json_decode($req->getBody()->getContents(), true);

            if (!$data) {
                throw new Exception("JSON inválido.", 2);
            }

            return $this->json($resp, $this->getController()->cambiarEstado($args['id'], $data));
        } catch (Exception $e) {
            return $this->handleError($resp, $e);
        }
    }

}