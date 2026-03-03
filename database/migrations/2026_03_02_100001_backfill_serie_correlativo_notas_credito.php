<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Rellena serie y correlativo en notas de crédito a partir de descripcion (ej: "numero BC01-1,").
     */
    public function up(): void
    {
        $rows = DB::table('sunat_responses')
            ->where('tipo_documento', 'nota_credito')
            ->whereNotNull('descripcion')
            ->get(['id', 'descripcion']);

        foreach ($rows as $row) {
            if (preg_match('/numero\s+([A-Za-z0-9]+)-(\d+)/', $row->descripcion, $m)) {
                DB::table('sunat_responses')
                    ->where('id', $row->id)
                    ->update(['serie' => $m[1], 'correlativo' => $m[2]]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
