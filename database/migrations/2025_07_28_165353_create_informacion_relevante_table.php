<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInformacionRelevanteTable extends Migration
{
    public function up()
    {
        Schema::create('informacion_relevante', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('contact_id');

            $table->enum('experiencia_tecnico_superior', ['SI', 'NO', 'NO_APLICA']);
            $table->enum('experiencia_tecnologo_superior', ['SI', 'NO', 'NO_APLICA']);
            $table->enum('experiencia_tercer_nivel', ['SI', 'NO', 'NO_APLICA']);

            $table->enum('capacitacion_ultimos_5_anios_100h', ['SI', 'NO']);

            $table->boolean('experiencia_3_anios_gestion_educativa');
            $table->boolean('experiencia_3_anios_docencia_investigacion');

            $table->timestamps();

            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('informacion_relevante');
    }
}
