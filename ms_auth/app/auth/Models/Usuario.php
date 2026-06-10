<?php

namespace App\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'correo',
        'usuario',
        'contrasena',
        'rol',
        'estado'
    ];

    protected $hidden = [
        'contrasena',
        'token'
    ];
}