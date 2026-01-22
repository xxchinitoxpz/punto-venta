<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'razon_social',
        'nombre_comercial',
        'ruc',
        'direccion',
        'ubigueo',
        'departamento',
        'provincia',
        'distrito',
        'urbanizacion',
        'cod_local',
        'logo_path',
        'sol_user',
        'sol_pass',
        'client_id',
        'client_secret',
        'cert_path',
        'production',
    ];

    protected $casts = [
        'production' => 'boolean',
    ];

    /**
     * Relación con Branches
     */
    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class, 'empresa_id');
    }
}
