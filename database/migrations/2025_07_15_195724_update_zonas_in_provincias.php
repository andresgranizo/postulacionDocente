<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // ZONA 1
        DB::table('provincias')->whereIn('nombre', [
            'ESMERALDAS',
            'IMBABURA',
            'CARCHI',
            'SUCUMBIOS'
        ])->update(['zona' => 1]);

        // ZONA 2
        DB::table('provincias')->whereIn('nombre', [
            'PICHINCHA',
            'NAPO',
            'ORELLANA'
        ])->update(['zona' => 2]);

        // ZONA 3
        DB::table('provincias')->whereIn('nombre', [
            'COTOPAXI',
            'TUNGURAHUA',
            'CHIMBORAZO',
            'PASTAZA'
        ])->update(['zona' => 3]);

        // ZONA 4
        DB::table('provincias')->whereIn('nombre', [
            'MANABI',
            'SANTO DOMINGO DE LOS TSACHILAS'
        ])->update(['zona' => 4]);

        // ZONA 5
        DB::table('provincias')->whereIn('nombre', [
            'SANTA ELENA',
            'GUAYAS',
            'BOLIVAR',
            'LOS RIOS',
            'GALAPAGOS'
        ])->update(['zona' => 5]);

        // ZONA 6
        DB::table('provincias')->whereIn('nombre', [
            'CAÑAR',
            'AZUAY',
            'MORONA SANTIAGO'
        ])->update(['zona' => 6]);

        // ZONA 7
        DB::table('provincias')->whereIn('nombre', [
            'EL ORO',
            'LOJA',
            'Zamora Chinchipe'
        ])->update(['zona' => 7]);
    }

    public function down()
    {
        // Revertir: poner las zonas en null
        DB::table('provincias')->update(['zona' => null]);
    }
};
