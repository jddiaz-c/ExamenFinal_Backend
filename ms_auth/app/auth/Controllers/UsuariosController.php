<?php
namespace App\Auth\Controllers;

use App\Auth\Models\Usuario;
use MsCore\Controllers\BaseController;
use Exception;

class UsuariosController extends BaseController
{

    protected string $model = Usuario::class;
    protected const RULES = [
            'nombre' => [
            'required' => true,
            'type' => 'string',
            'min' => 2,
            'max' => 100,
            'regex' => '/^[a-zA-ZÁÉÍÓÚáéíóúñÑ ]+$/'
        ],
        'correo' => [
            'required' => true,
            'type' => 'email',
            'max' => 150
        ],
        'usuario' => [
            'required' => true,
            'type' => 'string',
            'min' => 5,
            'max' => 30,
            'regex' => '/^[a-zA-Z0-9_]+$/'
        ],
        'contrasena' => [
            'required' => true,
            'type' => 'string',
            'min' => 4,
            'max' => 255,
            'regex' => '/^[a-zA-Z0-9_!@#$%^&*()-+]+$/'
        ],
        'rol' => [
            'required' => true,
            'enum' => ['administrador', 'gestion_humana', 'empleado']
        ],
        'estado' => [
            'required' => false,
            'enum' => ['activo', 'inactivo']
        ]
    ];

    // CREATE

    protected function beforeCreate(array &$data): void
    {
        $data['correo'] = strtolower(trim($data['correo']));
        $data['usuario'] = strtolower(trim($data['usuario']));

        if (Usuario::where('correo', $data['correo'])->exists()) {
            throw new Exception("El correo ya está registrado.", 2);
        }

        if (Usuario::where('usuario', $data['usuario'])->exists()) {
            throw new Exception("El nombre de usuario ya está en uso.", 2);
        }
    }

    // UPDATE

    protected function beforeUpdate(array &$data, $model): void
    {
        if (isset($data['correo'])) {
            $data['correo'] = strtolower(trim($data['correo']));

            $existe = Usuario::where('correo', $data['correo'])
                ->where('id', '!=', $model->id)
                ->exists();

            if ($existe) {
                throw new Exception("El correo ya está en uso.", 2);
            }
        }

        if (isset($data['usuario'])) {
            $existe = Usuario::where('usuario', $data['usuario'])
                ->where('id', '!=', $model->id)
                ->exists();

            if ($existe) {
                throw new Exception("El nombre de usuario ya está en uso.", 2);
            }
        }
    }

    // CAMBIAR ESTADO

    public function cambiarEstado($id, array $data): Usuario
    {
        $usuario = $this->getOne($id);

        if (empty($data['estado']) || !in_array($data['estado'], ['activo', 'inactivo'])) {
            throw new Exception("Estado inválido. Debe ser 'activo' o 'inactivo'.", 2);
        }

        $usuario->estado = $data['estado'];
        $usuario->save();

        return $usuario;
    }

}