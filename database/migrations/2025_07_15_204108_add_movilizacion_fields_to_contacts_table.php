<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->enum('disponibilidad_movilizacion', ['si', 'no'])->nullable()->after('zona_id');
            $table->json('provincias_movilizacion')->nullable()->after('disponibilidad_movilizacion');
            $table->string('zonas_movilizacion')->nullable()->after('provincias_movilizacion');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('disponibilidad_movilizacion');
            $table->dropColumn('provincias_movilizacion');
            $table->dropColumn('zonas_movilizacion');
        });
    }
};
