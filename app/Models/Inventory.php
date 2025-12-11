<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $fillable = [
        'stock',
        'fecha_vencimiento',
        'producto_id',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'stock' => 'integer',
    ];

    /**
     * Relación con Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'producto_id');
    }
}
