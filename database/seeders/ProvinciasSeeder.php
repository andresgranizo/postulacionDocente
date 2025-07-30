<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinciasSeeder extends Seeder
{
    public function run()
    {
        DB::table('provincias')->insert([
            ['id' => 343, 'nombre' => 'AZUAY', 'codigo' => 1],
            ['id' => 344, 'nombre' => 'BOLIVAR', 'codigo' => 2],
            ['id' => 345, 'nombre' => 'CAÑAR', 'codigo' => 3],
            ['id' => 346, 'nombre' => 'CARCHI', 'codigo' => 4],
            ['id' => 347, 'nombre' => 'COTOPAXI', 'codigo' => 5],
            ['id' => 348, 'nombre' => 'CHIMBORAZO', 'codigo' => 6],
            ['id' => 349, 'nombre' => 'EL ORO', 'codigo' => 7],
            ['id' => 350, 'nombre' => 'ESMERALDAS', 'codigo' => 8],
            ['id' => 351, 'nombre' => 'GUAYAS', 'codigo' => 9],
            ['id' => 352, 'nombre' => 'IMBABURA', 'codigo' => 10],
            ['id' => 353, 'nombre' => 'LOJA', 'codigo' => 11],
            ['id' => 354, 'nombre' => 'LOS RIOS', 'codigo' => 12],
            ['id' => 355, 'nombre' => 'MANABI', 'codigo' => 13],
            ['id' => 356, 'nombre' => 'MORONA SANTIAGO', 'codigo' => 14],
            ['id' => 357, 'nombre' => 'NAPO', 'codigo' => 15],
            ['id' => 358, 'nombre' => 'PASTAZA', 'codigo' => 16],
            ['id' => 359, 'nombre' => 'PICHINCHA', 'codigo' => 17],
            ['id' => 360, 'nombre' => 'TUNGURAHUA', 'codigo' => 18],
            ['id' => 361, 'nombre' => 'Zamora Chinchipe', 'codigo' => 19],
            ['id' => 362, 'nombre' => 'GALAPAGOS', 'codigo' => 20],
            ['id' => 363, 'nombre' => 'SUCUMBIOS', 'codigo' => 21],
            ['id' => 364, 'nombre' => 'ORELLANA', 'codigo' => 22],
            ['id' => 365, 'nombre' => 'SANTO DOMINGO DE LOS TSACHILAS', 'codigo' => 23],
            ['id' => 366, 'nombre' => 'SANTA ELENA', 'codigo' => 24],
        ]);
    }
}
