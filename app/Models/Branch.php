<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'empresa_id',
    ];

    /**
     * Relación con Company
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'empresa_id');
    }

    /**
     * Relación con DocumentSeries
     */
    public function documentSeries()
    {
        return $this->hasMany(DocumentSeries::class, 'sucursal_id');
    }

    /**
     * Relación con Users
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'sucursal_id');
    }
}
