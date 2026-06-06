<?php

namespace App\Seguimiento\Handlers;
use App\Seguimiento\Controllers\SeguimientoController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;
use MsCore\Handlers\BaseHandler;
class SeguimientoHandler extends BaseHandler
{
    protected function getController(): SeguimientoController
    {
        return new SeguimientoController();
    }

    public function buscar(Request $req, Response $resp): Response
    {
        try {
            $params = $req->getQueryParams();
            return $this->json($resp, $this->getController()->buscar($params));
        } catch (Exception $e) {
            return $this->handleError($resp, $e);
        }
    }
}