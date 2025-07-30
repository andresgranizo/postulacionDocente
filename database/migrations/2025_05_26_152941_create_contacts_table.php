<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_documento', 20); // Tipo de documento, por defecto 'cedula'
            $table->string('nombres_apellidos');
            $table->string('cedula', 20);
            $table->string('correo');
            $table->string('codigo_dactilar', 30);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contacts');
    }
};
