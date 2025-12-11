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
        Schema::create('cash_box_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesion_caja_id')->constrained('cash_box_sessions')->onDelete('cascade');
            $table->enum('tipo', ['ingreso', 'salida']);
            $table->decimal('monto', 10, 2);
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia', 'billetera_virtual']);
            $table->text('descripcion');
            $table->morphs('origen'); // Esto crea origen_tipo y origen_id
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_box_movements');
    }
};
