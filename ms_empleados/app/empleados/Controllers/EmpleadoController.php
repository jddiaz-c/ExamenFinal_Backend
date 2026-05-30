<?php
namespace App\empleados\Controllers;

use App\empleados\Models\Empleado;
use Exception;

class EmpleadoController extends BaseController
{

    protected string $model = Empleado::class;
    protected const RULES = [
        'nombres' => [
            'required' => true,
            'type' => 'string',
            'min' => 2,
            'max' => 100,
            'regex' => '/^[a-zA-ZÁÉÍÓÚáéíóúñÑ ]+$/'
        ],
        'apellidos' => [
            'required' => true,
            'type' => 'string',
            'min' => 2,
            'max' => 100,
            'regex' => '/^[a-zA-ZÁÉÍÓÚáéíóúñÑ ]+$/'
        ],
        'documento' => [
            'required' => true,
            'type' => 'string',
            'min' => 5,
            'max' => 30,
            'regex' => '/^[0-9]+$/'
        ],
        'correo' => [
            'required' => true,
            'type' => 'email',
            'max' => 150
        ],
        'telefono' => [
            'required' => true,
            'type' => 'string',
            'min' => 7,
            'max' => 30,
            'regex' => '/^[0-9]+$/'
        ],
        'cargo' => [
            'required' => true,
            'type' => 'string',
            'max' => 100
        ],
        'area' => [
            'required' => true,
            'type' => 'string',
            'max' => 100
        ],
        'fecha_ingreso' => [
            'required' => true,
            'type' => 'date'
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

        if (Empleado::where('cedula', $data['cedula'])->exists()) {
            throw new Exception("La cédula ya está registrada.", 2);
        }

        if (Empleado::where('correo', $data['correo'])->exists()) {
            throw new Exception("El correo ya está registrado.", 2);
        }
    }

    // UPDATE

    protected function beforeUpdate(array &$data, $model): void
    {
        if (isset($data['correo'])) {
            $data['correo'] = strtolower(trim($data['correo']));

            $existe = Empleado::where('correo', $data['correo'])
                ->where('id', '!=', $model->id)
                ->exists();

            if ($existe) {
                throw new Exception("El correo ya está en uso.", 2);
            }
        }

        if (isset($data['documento'])) {
            $existe = Empleado::where('documento', $data['documento'])
                ->where('id', '!=', $model->id)
                ->exists();

            if ($existe) {
                throw new Exception("El documento ya está en uso.", 2);
            }
        }
    }

    // CAMBIAR ESTADO

    public function cambiarEstado($id, array $data): Empleado
    {
        /** @var Empleado $empleado */
        $empleado = $this->getOne($id);

        if (empty($data['estado']) || !in_array($data['estado'], ['activo', 'inactivo'])) {
            throw new Exception("Estado inválido. Debe ser 'activo' o 'inactivo'.", 2);
        }

        $empleado->estado = $data['estado'];
        $empleado->save();

        return $empleado;
    }

    // BUSCAR

    public function buscar(array $params): object
    {
        $query = Empleado::query();

        if (!empty($params['documento'])) {
            $query->where('documento', $params['documento']);
        }

        if (!empty($params['area'])) {
            $query->where('area', 'like', '%' . $params['area'] . '%');
        }

        if (!empty($params['estado'])) {
            $query->where('estado', $params['estado']);
        }

        return $query->get();
    }
}