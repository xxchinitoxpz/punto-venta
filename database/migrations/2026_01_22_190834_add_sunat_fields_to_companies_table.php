<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('nombre_comercial')->nullable()->after('razon_social');
            $table->string('ubigueo', 6)->nullable()->after('direccion');
            $table->string('departamento')->nullable()->after('ubigueo');
            $table->string('provincia')->nullable()->after('departamento');
            $table->string('distrito')->nullable()->after('provincia');
            $table->string('urbanizacion')->nullable()->after('distrito');
            $table->string('cod_local', 4)->default('0000')->after('urbanizacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'nombre_comercial',
                'ubigueo',
                'departamento',
                'provincia',
                'distrito',
                'urbanizacion',
                'cod_local'
            ]);
        });
    }
};
