<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CashBoxMovement extends Model
{
    protected $fillable = [
        'sesion_caja_id',
        'tipo',
        'monto',
        'metodo_pago',
        'descripcion',
        'origen_type',
        'origen_id',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(CashBoxSession::class, 'sesion_caja_id');
    }

    public function origen(): MorphTo
    {
        return $this->morphTo();
    }
}
