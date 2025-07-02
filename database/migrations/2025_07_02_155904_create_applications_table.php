<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('contacts')->onDelete('cascade');
            $table->foreignId('process_id')->constrained('processes')->onDelete('cascade');
            $table->enum('status', ['pendiente', 'finalizado'])->default('pendiente');
            $table->timestamp('applied_at')->useCurrent();
            $table->timestamps();
            $table->unique(['contact_id', 'process_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('applications');
    }
};
