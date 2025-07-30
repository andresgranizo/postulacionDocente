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
            $table->string('telefono')->nullable()->after('correo');
            $table->unsignedBigInteger('provincia_id')->nullable()->after('telefono');
            $table->unsignedBigInteger('canton_id')->nullable()->after('provincia_id');
            $table->unsignedBigInteger('zona_id')->nullable()->after('canton_id');
        });
    }

    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('telefono');
            $table->dropColumn('provincia_id');
            $table->dropColumn('canton_id');
            $table->dropColumn('zona_id');
        });
    }
};
