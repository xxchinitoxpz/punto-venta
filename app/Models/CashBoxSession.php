<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashBoxSession extends Model
{
    protected $fillable = [
        'fecha_hora_apertura',
        'monto_apertura_efectivo',
        'monto_cierre_efectivo_contado',
        'fecha_hora_cierre',
        'estado',
        'caja_id',
        'usuario_id',
    ];

    protected $casts = [
        'fecha_hora_apertura' => 'datetime',
        'fecha_hora_cierre' => 'datetime',
        'monto_apertura_efectivo' => 'decimal:2',
        'monto_cierre_efectivo_contado' => 'decimal:2',
    ];

    public function cashBox(): BelongsTo
    {
        return $this->belongsTo(CashBox::class, 'caja_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(CashBoxMovement::class, 'sesion_caja_id');
    }

    public function getMontoEsperadoEfectivoAttribute(): float
    {
        $ingresosEfectivo = $this->movements()
            ->where('tipo', 'ingreso')
            ->where('metodo_pago', 'efectivo')
            ->sum('monto');

        $salidasEfectivo = $this->movements()
            ->where('tipo', 'salida')
            ->where('metodo_pago', 'efectivo')
            ->sum('monto');

        return (float) ($this->monto_apertura_efectivo + $ingresosEfectivo - $salidasEfectivo);
    }
}
