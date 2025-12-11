<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentSeries extends Model
{
    protected $fillable = [
        'tipo_comprobante',
        'serie',
        'ultimo_correlativo',
        'sucursal_id',
    ];

    protected $casts = [
        'ultimo_correlativo' => 'integer',
    ];

    /**
     * Relación con Branch
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'sucursal_id');
    }
}
