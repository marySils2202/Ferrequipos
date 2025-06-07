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
        Schema::table('detalle_compra', function (Blueprint $table) {
            $table->foreign(['id_producto'], 'fk_detalle_producto')->references(['id_producto'])->on('productos')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign(['id_producto'], 'fk_producto_detalle')->references(['id_producto'])->on('productos')->onUpdate('no action')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalle_compra', function (Blueprint $table) {
            $table->dropForeign('fk_detalle_producto');
            $table->dropForeign('fk_producto_detalle');
        });
    }
};
