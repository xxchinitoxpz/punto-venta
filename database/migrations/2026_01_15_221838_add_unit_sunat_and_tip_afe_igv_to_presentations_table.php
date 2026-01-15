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
        Schema::table('presentations', function (Blueprint $table) {
            $table->foreignId('unit_sunat_id')->nullable()->constrained('unit_sunat')->onDelete('set null');
            $table->string('tipAfeIgv')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presentations', function (Blueprint $table) {
            $table->dropForeign(['unit_sunat_id']);
            $table->dropColumn(['unit_sunat_id', 'tipAfeIgv']);
        });
    }
};
