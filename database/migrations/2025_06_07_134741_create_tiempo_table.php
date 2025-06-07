<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tiempo', function (Blueprint $table) {
            $table->time('hora');
            $table->tinyInteger('dia');
            $table->tinyInteger('mes');
            $table->year('anio');

            $table->primary(['hora', 'dia', 'mes', 'anio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiempo');
    }
};
