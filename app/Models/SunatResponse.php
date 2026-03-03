<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SunatResponse extends Model
{
    protected $fillable = [
        'sale_id',
        'tipo_documento',
        'serie',
        'correlativo',
        'estado',
        'codigo',
        'descripcion',
        'observaciones',
        'xml',
        'hash',
        'cdr_zip',
        'error_code',
        'error_message',
    ];

    protected $casts = [
        'observaciones' => 'array',
        'codigo' => 'integer',
    ];

    /**
     * Relación con Sale
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
