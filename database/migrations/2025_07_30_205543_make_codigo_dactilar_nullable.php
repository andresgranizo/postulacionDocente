<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up()
    {
        DB::statement('ALTER TABLE contacts ALTER COLUMN codigo_dactilar DROP NOT NULL;');
    }

    public function down()
    {
        DB::statement('ALTER TABLE contacts ALTER COLUMN codigo_dactilar SET NOT NULL;');
    }
};
