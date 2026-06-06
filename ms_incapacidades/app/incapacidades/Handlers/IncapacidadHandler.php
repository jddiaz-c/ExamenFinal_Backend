<?php

namespace App\Incapacidades\Handlers;
use App\Incapacidades\Controllers\IncapacidadController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;
use MsCore\Handlers\BaseHandler;
class IncapacidadHandler extends BaseHandler
{
    protected function getController(): IncapacidadController
    {
        return new IncapacidadController();
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

    public function finalizar(Request $req, Response $resp, array $args): Response
    {
        try {
            return $this->json($resp, $this->getController()->finalizar($args['id']));
        } catch (Exception $e) {
            return $this->handleError($resp, $e);
        }
    }
}