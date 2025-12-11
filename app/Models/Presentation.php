<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Presentation extends Model
{
    protected $fillable = [
        'product_id',
        'nombre',
        'barcode',
        'precio_venta',
        'unidades',
    ];

    protected $casts = [
        'precio_venta' => 'decimal:2',
        'unidades' => 'decimal:2',
    ];

    /**
     * Relación con Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relación muchos-a-muchos con Promotion a través de promotion_details
     */
    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class, 'promotion_details')
            ->withPivot('cantidad_requerida')
            ->withTimestamps();
    }
}
