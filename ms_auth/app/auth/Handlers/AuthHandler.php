<?php

namespace App\Auth\Handlers;

use App\Auth\Controllers\AuthController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;

class AuthHandler
{
    private AuthController $controller;

    public function __construct()
    {
        $this->controller = new AuthController();
    }

    protected function json(Response $resp, array $data, int $status = 200): Response
    {
        $resp->getBody()->write(json_encode($data));
        return $resp
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json');
    }

    protected function handleError(Response $resp, Exception $ex): Response
    {
        if ($ex->getCode() === 1) {
            return $this->json($resp, ['error' => $ex->getMessage()], 404);
        }

        if ($ex->getCode() >= 2) {
            return $this->json($resp, ['error' => $ex->getMessage()], 400);
        }

        return $this->json($resp, ['error' => 'Error interno del servidor.'], 500);
    }

    public function login(Request $req, Response $resp): Response
    {
        try {
            $data = json_decode($req->getBody()->getContents(), true);

            if (!$data) {
                throw new Exception("JSON inválido.", 2);
            }

            $result = $this->controller->login($data);
            return $this->json($resp, $result, 200);

        } catch (Exception $e) {
            return $this->handleError($resp, $e);
        }
    }

    public function logout(Request $req, Response $resp): Response
    {
        try {
            $token = $req->getHeaderLine('Authorization');

            if (empty($token)) {
                throw new Exception("Token no proporcionado.", 2);
            }

            $this->controller->logout($token);
            return $this->json($resp, ['message' => 'Sesión cerrada correctamente.'], 200);

        } catch (Exception $e) {
            return $this->handleError($resp, $e);
        }
    }

    public function validate(Request $req, Response $resp): Response{
        try {
            $token = $req->getHeaderLine('Authorization');

            if (empty($token)) {
                throw new Exception("Token no proporcionado.", 2);
            }

            $result = $this->controller->validate($token);
            return $this->json($resp, $result, 200);

        } catch (Exception $e) {
            return $this->handleError($resp, $e);
        }
    }
}