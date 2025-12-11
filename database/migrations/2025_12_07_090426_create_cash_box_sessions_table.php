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
        Schema::create('cash_box_sessions', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha_hora_apertura');
            $table->decimal('monto_apertura_efectivo', 10, 2);
            $table->decimal('monto_cierre_efectivo_contado', 10, 2)->nullable();
            $table->timestamp('fecha_hora_cierre')->nullable();
            $table->enum('estado', ['abierta', 'cerrada'])->default('abierta');
            $table->foreignId('caja_id')->constrained('cash_boxes')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_box_sessions');
    }
};
