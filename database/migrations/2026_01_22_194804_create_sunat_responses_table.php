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
        Schema::create('sunat_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            
            // Estado de la respuesta
            $table->enum('estado', ['aceptada', 'rechazada', 'excepcion', 'error_conexion'])->nullable();
            
            // Código y descripción de SUNAT
            $table->integer('codigo')->nullable();
            $table->text('descripcion')->nullable();
            
            // Observaciones (JSON)
            $table->json('observaciones')->nullable();
            
            // Datos técnicos
            $table->text('xml')->nullable();
            $table->string('hash')->nullable();
            $table->text('cdr_zip')->nullable(); // Base64
            
            // Errores
            $table->string('error_code')->nullable();
            $table->text('error_message')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index('sale_id');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sunat_responses');
    }
};
