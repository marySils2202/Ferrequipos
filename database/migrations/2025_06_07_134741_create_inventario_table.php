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
        Schema::create('inventario', function (Blueprint $table) {
            $table->integer('id_inventario', true);
            $table->integer('id_producto')->index('id_producto');
            $table->integer('cantidad_stock')->default(0);
            $table->timestamp('fecha_actualizacion')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->unique(['id_producto'], 'ux_inventario_id_producto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario');
    }
};
