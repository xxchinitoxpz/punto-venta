<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'tipo_comprobante',
        'serie',
        'correlativo',
        'total_gravado',
        'total_igv',
        'total_venta',
        'cliente_id',
        'usuario_id',
        'sesion_caja_id',
        'estado',
    ];

    protected $casts = [
        'correlativo' => 'integer',
        'total_gravado' => 'decimal:2',
        'total_igv' => 'decimal:2',
        'total_venta' => 'decimal:2',
    ];

    /**
     * Relación con Client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'cliente_id');
    }

    /**
     * Relación con User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación con CashBoxSession
     */
    public function cashBoxSession(): BelongsTo
    {
        return $this->belongsTo(CashBoxSession::class, 'sesion_caja_id');
    }

    /**
     * Relación con SaleDetail
     */
    public function details(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }

    /**
     * Relación con SalePayment
     */
    public function payments(): HasMany
    {
        return $this->hasMany(SalePayment::class);
    }
}
