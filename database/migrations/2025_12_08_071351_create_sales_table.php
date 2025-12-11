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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_comprobante', ['factura', 'boleta', 'ticket']);
            $table->string('serie', 4);
            $table->integer('correlativo');
            $table->unique(['tipo_comprobante', 'serie', 'correlativo']);

            $table->decimal('total_gravado', 10, 2);
            $table->decimal('total_igv', 10, 2);
            $table->decimal('total_venta', 10, 2);

            $table->foreignId('cliente_id')->nullable()->constrained('clients')->onDelete('restrict');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('sesion_caja_id')->constrained('cash_box_sessions')->onDelete('restrict');

            $table->enum('estado', ['registrada', 'anulada'])->default('registrada');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
