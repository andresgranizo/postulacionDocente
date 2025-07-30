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
        Schema::table('contacts', function (Blueprint $table) {
            $table->json('cuenta_con')->nullable();
            $table->string('reconocimiento_archivo')->nullable();
            $table->boolean('experiencia_gestion')->nullable();
            $table->boolean('experiencia_docencia')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['cuenta_con', 'reconocimiento_archivo', 'experiencia_gestion', 'experiencia_docencia']);
        });
    }
};
