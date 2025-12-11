<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
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
