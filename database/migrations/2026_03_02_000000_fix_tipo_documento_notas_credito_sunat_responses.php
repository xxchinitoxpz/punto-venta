<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Marca como nota_credito las respuestas de SUNAT que corresponden a Notas de Crédito
     * pero quedaron con tipo_documento = 'comprobante' (p. ej. creadas antes del campo).
     */
    public function up(): void
    {
        DB::table('sunat_responses')
            ->where('tipo_documento', 'comprobante')
            ->where(function ($q) {
                $q->where('descripcion', 'like', '%Nota de Credito%')
                    ->orWhere('descripcion', 'like', '%Nota de Crédito%');
            })
            ->update(['tipo_documento' => 'nota_credito']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversible sin criterio único
    }
};
