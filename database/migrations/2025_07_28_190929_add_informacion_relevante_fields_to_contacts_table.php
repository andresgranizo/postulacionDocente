<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('exp_tecnico_superior')->nullable();
            $table->string('exp_tecnologo_superior')->nullable();
            $table->string('exp_tercer_nivel')->nullable();
            $table->string('capacitacion_100h')->nullable();
            $table->string('exp_gestion_educativa')->nullable();
            $table->string('exp_docencia_investigacion')->nullable();
        });
    }

    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn([
                'exp_tecnico_superior',
                'exp_tecnologo_superior',
                'exp_tercer_nivel',
                'capacitacion_100h',
                'exp_gestion_educativa',
                'exp_docencia_investigacion'
            ]);
        });
    }
};
