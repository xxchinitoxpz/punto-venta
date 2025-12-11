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
        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');

            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'billetera_virtual', 'otro']);
            $table->decimal('monto_pagado', 10, 2);
            $table->string('referencia')->nullable();

            // CLAVE: Enlaza al movimiento de caja para trazabilidad total de CUALQUIER pago.
            $table->foreignId('cash_box_movement_id')
                  ->nullable()
                  ->constrained('cash_box_movements')
                  ->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_payments');
    }
};
