<?php

use Illuminate\Support\Facades\DB;

return new class extends \Illuminate\Database\Migrations\Migration {
    public function up(): void
    {

        DB::statement("UPDATE contacts SET experiencia_gestion = FALSE WHERE experiencia_gestion IS NULL");
        DB::statement("UPDATE contacts SET experiencia_docencia = FALSE WHERE experiencia_docencia IS NULL");


        DB::statement("ALTER TABLE contacts ALTER COLUMN experiencia_gestion SET NOT NULL");
        DB::statement("ALTER TABLE contacts ALTER COLUMN experiencia_docencia SET NOT NULL");


        DB::statement("ALTER TABLE contacts ALTER COLUMN experiencia_gestion SET DEFAULT FALSE");
        DB::statement("ALTER TABLE contacts ALTER COLUMN experiencia_docencia SET DEFAULT FALSE");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE contacts ALTER COLUMN experiencia_gestion DROP DEFAULT");
        DB::statement("ALTER TABLE contacts ALTER COLUMN experiencia_docencia DROP DEFAULT");

        DB::statement("ALTER TABLE contacts ALTER COLUMN experiencia_gestion DROP NOT NULL");
        DB::statement("ALTER TABLE contacts ALTER COLUMN experiencia_docencia DROP NOT NULL");
    }
};
