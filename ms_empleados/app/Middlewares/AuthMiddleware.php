<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface as Response;

class AuthMiddleware
{
    public function __invoke(Request $request, Handler $handler): Response
    {
        $token = $request->getHeaderLine('Authorization');

        if (empty($token)) {
            return $this->unauthorized('Token no proporcionado.');
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Authorization: $token\r\nContent-Type: application/json\r\n",
                'ignore_errors' => true
            ]
        ]);

        $result = file_get_contents($_ENV['AUTH_URL'] . '/auth/validate', false, $context);

        if ($result === false) {
            return $this->unauthorized('No se pudo conectar al servicio de autenticación.');
        }

        $data = json_decode($result, true);

        if (empty($data['valid']) || $data['valid'] !== true) {
            return $this->unauthorized('Token inválido o sesión expirada.');
        }

        return $handler->handle($request);
    }

    private function unauthorized(string $message): Response
    {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(
            ['error' => $message],
            JSON_UNESCAPED_UNICODE
        ));
        return $response
            ->withStatus(401)
            ->withHeader('Content-Type', 'application/json');
    }
}