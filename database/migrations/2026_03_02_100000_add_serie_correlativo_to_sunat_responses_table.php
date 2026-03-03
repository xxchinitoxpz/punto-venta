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
        Schema::table('sunat_responses', function (Blueprint $table) {
            $table->string('serie', 10)->nullable()->after('tipo_documento');
            $table->string('correlativo', 20)->nullable()->after('serie');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sunat_responses', function (Blueprint $table) {
            $table->dropColumn(['serie', 'correlativo']);
        });
    }
};
