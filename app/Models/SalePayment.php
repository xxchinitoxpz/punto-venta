<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalePayment extends Model
{
    protected $fillable = [
        'sale_id',
        'metodo_pago',
        'monto_pagado',
        'referencia',
        'cash_box_movement_id',
    ];

    protected $casts = [
        'monto_pagado' => 'decimal:2',
    ];

    /**
     * Relación con Sale
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Relación con CashBoxMovement
     */
    public function cashBoxMovement(): BelongsTo
    {
        return $this->belongsTo(CashBoxMovement::class);
    }
}
