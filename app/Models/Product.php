<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria_id',
        'marca_id',
    ];

    /**
     * Relación con Category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'categoria_id');
    }

    /**
     * Relación con Brand
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'marca_id');
    }

    /**
     * Relación con Presentations
     */
    public function presentations(): HasMany
    {
        return $this->hasMany(Presentation::class);
    }
}
