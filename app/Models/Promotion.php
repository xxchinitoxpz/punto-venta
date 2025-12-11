<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Promotion extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio_promocional',
        'fecha_inicio',
        'fecha_fin',
        'activa',
    ];

    protected $casts = [
        'precio_promocional' => 'decimal:2',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activa' => 'boolean',
    ];

    /**
     * Relación muchos-a-muchos con Presentation a través de promotion_details
     */
    public function presentations(): BelongsToMany
    {
        return $this->belongsToMany(Presentation::class, 'promotion_details')
            ->withPivot('cantidad_requerida')
            ->withTimestamps();
    }
}
