<?php
namespace App\Seguimiento\Controllers;

use App\Seguimiento\Models\Seguimiento;
use MsCore\Controllers\BaseController;
use Exception;

class SeguimientoController extends BaseController
{
    protected string $model = Seguimiento::class;
    protected const RULES = [
        'incapacidad_id' => [
            'required' => true,
            'type' => 'integer'
        ],
        'fecha' => [
            'required' => true,
            'type' => 'date'
        ],
        'comentario' => [
            'required' => true,
            'type' => 'string',
            'max' => 1000
        ],
        'estado' => [
            'required' => true,
            'enum' => ['registrada', 'en_revision', 'aprobada', 'rechazada', 'finalizada']
        ],
        'usuario_responsable' => [
            'required' => true,
            'type' => 'string',
            'max' => 100
        ]
    ];
    // BUSCAR
    public function buscar(array $params): object
    {
        $query = Seguimiento::query();

        if (!empty($params['incapacidad_id'])) {
            $query->where('incapacidad_id', $params['incapacidad_id']);
        }

        if (!empty($params['usuario_responsable'])) {
            $query->where('usuario_responsable', $params['usuario_responsable']);
        }

        return $query->get();
    }
}