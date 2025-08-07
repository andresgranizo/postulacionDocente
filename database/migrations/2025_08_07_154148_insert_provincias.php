<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('provincias')->insert([
            ['id' => 343, 'nombre' => 'AZUAY', 'codigo' => 1, 'zona' => 6],
            ['id' => 344, 'nombre' => 'BOLIVAR', 'codigo' => 2, 'zona' => 5],
            ['id' => 345, 'nombre' => 'CAÑAR', 'codigo' => 3, 'zona' => 6],
            ['id' => 346, 'nombre' => 'CARCHI', 'codigo' => 4, 'zona' => 1],
            ['id' => 347, 'nombre' => 'COTOPAXI', 'codigo' => 5, 'zona' => 3],
            ['id' => 348, 'nombre' => 'CHIMBORAZO', 'codigo' => 6, 'zona' => 3],
            ['id' => 349, 'nombre' => 'EL ORO', 'codigo' => 7, 'zona' => 7],
            ['id' => 350, 'nombre' => 'ESMERALDAS', 'codigo' => 8, 'zona' => 1],
            ['id' => 351, 'nombre' => 'GUAYAS', 'codigo' => 9, 'zona' => 5],
            ['id' => 352, 'nombre' => 'IMBABURA', 'codigo' => 10, 'zona' => 1],
            ['id' => 353, 'nombre' => 'LOJA', 'codigo' => 11, 'zona' => 7],
            ['id' => 354, 'nombre' => 'LOS RIOS', 'codigo' => 12, 'zona' => 5],
            ['id' => 355, 'nombre' => 'MANABI', 'codigo' => 13, 'zona' => 4],
            ['id' => 356, 'nombre' => 'MORONA SANTIAGO', 'codigo' => 14, 'zona' => 6],
            ['id' => 357, 'nombre' => 'NAPO', 'codigo' => 15, 'zona' => 2],
            ['id' => 358, 'nombre' => 'PASTAZA', 'codigo' => 16, 'zona' => 3],
            ['id' => 359, 'nombre' => 'PICHINCHA', 'codigo' => 17, 'zona' => 2],
            ['id' => 360, 'nombre' => 'TUNGURAHUA', 'codigo' => 18, 'zona' => 3],
            ['id' => 362, 'nombre' => 'GALAPAGOS', 'codigo' => 20, 'zona' => 5],
            ['id' => 363, 'nombre' => 'SUCUMBIOS', 'codigo' => 21, 'zona' => 1],
            ['id' => 364, 'nombre' => 'ORELLANA', 'codigo' => 22, 'zona' => 2],
            ['id' => 365, 'nombre' => 'SANTO DOMINGO DE LOS TSACHILAS', 'codigo' => 23, 'zona' => 4],
            ['id' => 366, 'nombre' => 'SANTA ELENA', 'codigo' => 24, 'zona' => 5],
            ['id' => 361, 'nombre' => 'ZAMORA CHINCHIPE', 'codigo' => 19, 'zona' => 7],
        ]);
    }

    public function down(): void
    {
        DB::table('provincias')->whereIn('id', [
            343, 344, 345, 346, 347, 348, 349, 350, 351, 352,
            353, 354, 355, 356, 357, 358, 359, 360, 361, 362,
            363, 364, 365, 366
        ])->delete();
    }
};
