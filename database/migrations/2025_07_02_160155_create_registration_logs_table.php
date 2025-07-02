<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('registration_logs', function (Blueprint $table) {
            $table->id();
            $table->string('cedula', 20);
            $table->string('nombres_apellidos')->nullable();
            $table->timestamp('attempted_at')->useCurrent();
            $table->string('reason');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('registration_logs');
    }
};
