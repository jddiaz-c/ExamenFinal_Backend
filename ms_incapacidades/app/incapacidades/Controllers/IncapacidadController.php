<?php
namespace App\Incapacidades\Controllers;

use App\Incapacidades\Models\Incapacidad;
use MsCore\Controllers\BaseController;
use Exception;

class IncapacidadController extends BaseController
{
    protected string $model = Incapacidad::class;
    protected const RULES = [
        'empleado_id' => [
            'required' => true,
            'type' => 'integer'
        ],
        'fecha_inicio' => [
            'required' => true,
            'type' => 'date'
        ],
        'fecha_fin' => [
            'required' => true,
            'type' => 'date'
        ],
        'tipo' => [
            'required' => true,
            'enum' => ['enfermedad_general', 'accidente_laboral', 'licencia_medica', 'incapacidad_temporal']
        ],
        'diagnostico_general' => [
            'required' => true,
            'type' => 'string',
            'max' => 500
        ],
        'entidad_medica' => [
            'required' => true,
            'type' => 'string',
            'max' => 150
        ],
        'observaciones' => [
            'required' => false,
            'type' => 'string'
        ],
        'estado' => [
            'required' => false,
            'enum' => ['registrada', 'en_revision', 'aprobada', 'rechazada', 'finalizada']
        ]
    ];
    // CREATE
    protected function beforeCreate(array &$data): void
    {
        $data['dias_incapacidad'] = $this->comprobarFechas($data['fecha_inicio'], $data['fecha_fin'], $data['empleado_id']);

    }

    // UPDATE
    protected function beforeUpdate(array &$data, $model): void
    {
        if (in_array($model->estado, ['rechazada', 'finalizada'])) {
            throw new Exception("Esta incapacidad ya no puede modificarse.", 2);
        }
        $camposNoEditables = ['empleado_id', 'tipo', 'diagnostico_general', 'entidad_medica', 'dias_incapacidad', 'estado'];
        $camposLlenos = [];
        foreach ($camposNoEditables as $campo) {
            if (isset($data[$campo])) {
                $camposLlenos[] = $campo;
            }
        }
        if (!empty($camposLlenos)) {
            throw new Exception("Los campos " . implode(', ', $camposLlenos) . " no pueden modificarse.", 2);
        }

        $data['dias_incapacidad'] = $this->comprobarFechas(
            $data['fecha_inicio'] ?? $model->fecha_inicio,
            $data['fecha_fin'] ?? $model->fecha_fin,
            $model->empleado_id,
            $model->id
        );
    }

    // BUSCAR

    public function buscar(array $params): object
    {
        $query = Incapacidad::query();

        if (!empty($params['empleado_id'])) {
            $query->where('empleado_id', $params['empleado_id']);
        }

        if (!empty($params['fecha_inicio']) && !empty($params['fecha_fin'])) {
            $query->where('fecha_inicio', '<=', $params['fecha_fin'])
                ->where('fecha_fin', '>=', $params['fecha_inicio']);
        }

        if (!empty($params['estado'])) {
            $query->where('estado', $params['estado']);
        }

        if (!empty($params['tipo'])) {
            $query->where('tipo', $params['tipo']);
        }


        return $query->get();
    }

    // CAMBIAR ESTADO 
    public function cambiarEstado($id, array $data): Incapacidad
    {
        $incapacidad = $this->getOne($id);

        if (
            empty($data['estado']) || !in_array($data['estado'], [
                'registrada',
                'en_revision',
                'aprobada',
                'rechazada',
                'finalizada'
            ])
        ) {
            throw new Exception("Estado inválido. Debe ser 'registrada', 'en_revision', 'aprobada' o 'rechazada'.", 2);
        }
        if ($data['estado'] === 'finalizada') {
            throw new Exception("Para finalizar la incapacidad, utilice la funcion especializada.", 2);
        }
        if (in_array($incapacidad->estado, ['rechazada', 'finalizada'])) {
            throw new Exception("Esta incapacidad ya no puede modificarse.", 2);
        }
        $incapacidad->estado = $data['estado'];
        $incapacidad->save();

        return $incapacidad;
    }

    // FINALIZAR

    public function finalizar($id): Incapacidad
    {
        $incapacidad = $this->getOne($id);

        if ($incapacidad->estado === 'finalizada') {
            throw new Exception("Esta incapacidad ya esta finalizada.", 2);
        }
        if ($incapacidad->estado !== 'aprobada') {
            throw new Exception("Solo las incapacidades aprobadas pueden finalizarse.", 2);
        }

        $incapacidad->estado = 'finalizada';
        $incapacidad->save();

        return $incapacidad;
    }

    // COMPROBAR FECHAS
    public function comprobarFechas($inicio, $fin, $empleado_id = null, $excluir_id = null): int
    {
        if ($fin < $inicio) {
            throw new Exception("La fecha fin no puede ser menor a la fecha inicio.", 2);
        }
        $inicioDate = new \DateTime($inicio);
        $finDate = new \DateTime($fin);

        if ($empleado_id !== null) {
            $query = Incapacidad::where('empleado_id', $empleado_id)
                ->where('fecha_inicio', '<=', $fin)
                ->where('fecha_fin', '>=', $inicio);

            if ($excluir_id !== null) {
                $query->where('id', '!=', $excluir_id);
            }

            if ($query->exists()) {
                throw new Exception("Ya existe una incapacidad para este empleado en ese rango de fechas.", 2);
            }
        }

        return $finDate->diff($inicioDate)->days + 1;
    }
}