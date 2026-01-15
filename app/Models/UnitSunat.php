<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitSunat extends Model
{
    protected $table = 'unit_sunat';

    protected $fillable = [
        'code',
        'description',
    ];
}
