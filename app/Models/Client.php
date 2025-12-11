<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'nombre_completo',
        'tipo_documento',
        'nro_documento',
        'telefono',
        'email',
        'direccion',
    ];
}
