<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends \Illuminate\Database\Migrations\Migration {
    public function up(): void
    {
        // 1️⃣ Borrar la foreign key actual
        Schema::table('documentos', function (Blueprint $table) {
            $table->dropForeign(['contact_id']);
        });

        // 2️⃣ Volver nullable
        DB::statement('ALTER TABLE documentos ALTER COLUMN contact_id DROP NOT NULL');

        // 3️⃣ Volver a agregar la foreign key
        Schema::table('documentos', function (Blueprint $table) {
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Quitar la foreign key
        Schema::table('documentos', function (Blueprint $table) {
            $table->dropForeign(['contact_id']);
        });

        // Volver NOT NULL
        DB::statement('ALTER TABLE documentos ALTER COLUMN contact_id SET NOT NULL');

        // Re-agregar la foreign key
        Schema::table('documentos', function (Blueprint $table) {
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
        });
    }
};
