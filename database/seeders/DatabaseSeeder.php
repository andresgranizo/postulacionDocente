<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call ([
            // Aquí puedes agregar otros seeders si es necesario
            // Ejemplo: UserSeeder::class,
            ProvinciasSeeder::class,
            CantonesSeeder::class,
        ]);
    }
}
