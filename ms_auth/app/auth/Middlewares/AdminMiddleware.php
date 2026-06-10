<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface as Response;
use App\Auth\Models\Usuario;

class AdminMiddleware
{
    public function __invoke(Request $request, Handler $handler): Response
    {
        $token = $request->getHeaderLine('Authorization');

        if (empty($token)) {
            return $this->forbidden('Token no proporcionado.');
        }

        /** @var Usuario $usuario */
        $usuario = Usuario::where('token', $token)
            ->where('sesion_activa', true)
            ->first();

        if (!$usuario) {
            return $this->forbidden('Token inválido o sesión expirada.');
        }

        if ($usuario->rol !== 'administrador') {
            return $this->forbidden('No tienes permisos para acceder a este recurso.');
        }

        return $handler->handle($request);
    }

    private function forbidden(string $message): Response
    {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(
            ['error' => $message],
            JSON_UNESCAPED_UNICODE
        ));
        return $response
            ->withStatus(403)
            ->withHeader('Content-Type', 'application/json');
    }
}