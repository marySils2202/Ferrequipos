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
        Schema::create('movimientos', function (Blueprint $table) {
            $table->integer('id_movimiento', true);
            $table->integer('id_producto')->index('movimientos_id_producto_idx');
            $table->enum('tipo', ['entrada', 'salida']);
            $table->integer('cantidad');
            $table->string('descripcion')->nullable();
            $table->timestamp('fecha')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
