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
        Schema::table('provincias', function (Blueprint $table) {
            $table->unsignedTinyInteger('zona')->nullable()->after('codigo');
        });
    }

    public function down()
    {
        Schema::table('provincias', function (Blueprint $table) {
            $table->dropColumn('zona');
        });
    }
};
