<?php

namespace App\Auth\Controllers;

use App\Auth\Models\Usuario;
use Exception;

class AuthController
{
    public function login(array $data): array
    {
        if (empty($data['usuario']) && empty($data['correo'])) {
            throw new Exception("Usuario o correo es obligatorio.", 2);
        }

        if (empty($data['contrasena'])) {
            throw new Exception("La contraseña es obligatoria.", 2);
        }

        $usuario = Usuario::where(function ($query) use ($data) {
            if (!empty($data['usuario'])) {
                $query->where('usuario', $data['usuario']);
            }
            if (!empty($data['correo'])) {
                $query->orWhere('correo', $data['correo']);
            }
        })->first();

        if (!$usuario) {
            throw new Exception("Credenciales incorrectas.", 2);
        }
        if ($usuario->estado !== 'activo') {
            throw new Exception("Usuario inactivo.", 2);
        }

        if ($data['contrasena'] !== $usuario->contrasena) {
            throw new Exception("Credenciales incorrectas.", 2);
        }

        $token = bin2hex(random_bytes(32));

        $usuario->token = $token;
        $usuario->sesion_activa = true;
        $usuario->save();

        return [
            'message' => 'Inicio de sesión exitoso.',
            'token' => $token,
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'correo' => $usuario->correo,
                'rol' => $usuario->rol
            ]
        ];
    }

    public function logout(string $token): void
    {
        $usuario = Usuario::where('token', $token)
            ->where('sesion_activa', true)
            ->first();

        if (!$usuario) {
            throw new Exception("Sesión no encontrada.", 1);
        }

        $usuario->token = null;
        $usuario->sesion_activa = false;
        $usuario->save();
    }

    public function validate(string $token): array
    {
        $usuario = Usuario::where('token', $token)
            ->where('sesion_activa', true)
            ->first();

        if (!$usuario) {
            throw new Exception("Token inválido o sesión expirada.", 1);
        }

        return [
            'valid' => true,
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'correo' => $usuario->correo,
                'rol' => $usuario->rol
            ]
        ];
    }
}