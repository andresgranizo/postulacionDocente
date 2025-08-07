<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('zonas')->insert([
            ['id' => 1, 'nombre' => 'Zona 1', 'created_at' => '2025-07-28 11:05:11.000', 'updated_at' => '2025-07-28 11:05:11.000'],
            ['id' => 2, 'nombre' => 'Zona 2', 'created_at' => '2025-07-28 11:05:11.000', 'updated_at' => '2025-07-28 11:05:11.000'],
            ['id' => 3, 'nombre' => 'Zona 3', 'created_at' => '2025-07-28 11:05:11.000', 'updated_at' => '2025-07-28 11:05:11.000'],
            ['id' => 4, 'nombre' => 'Zona 4', 'created_at' => '2025-07-28 11:05:11.000', 'updated_at' => '2025-07-28 11:05:11.000'],
            ['id' => 5, 'nombre' => 'Zona 5', 'created_at' => '2025-07-28 11:05:11.000', 'updated_at' => '2025-07-28 11:05:11.000'],
            ['id' => 6, 'nombre' => 'Zona 6', 'created_at' => '2025-07-28 11:05:11.000', 'updated_at' => '2025-07-28 11:05:11.000'],
            ['id' => 7, 'nombre' => 'Zona 7', 'created_at' => '2025-07-28 11:05:11.000', 'updated_at' => '2025-07-28 11:05:11.000'],
            ['id' => 8, 'nombre' => 'Zona 8', 'created_at' => '2025-07-28 11:05:11.000', 'updated_at' => '2025-07-28 11:05:11.000'],
            ['id' => 9, 'nombre' => 'Zona 9', 'created_at' => '2025-07-28 11:05:11.000', 'updated_at' => '2025-07-28 11:05:11.000'],
        ]);
    }

    public function down(): void
    {
        DB::table('zonas')->whereIn('id', range(1, 9))->delete();
    }
};
