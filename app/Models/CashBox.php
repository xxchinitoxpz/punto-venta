<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashBox extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'sucursal_id',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'sucursal_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(CashBoxSession::class, 'caja_id');
    }

    public function getCurrentSessionAttribute()
    {
        return $this->sessions()->where('estado', 'abierta')->first();
    }
}
