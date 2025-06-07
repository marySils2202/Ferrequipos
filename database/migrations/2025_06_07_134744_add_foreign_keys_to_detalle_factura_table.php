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
        Schema::table('detalle_factura', function (Blueprint $table) {
            $table->foreign(['id_factura'], 'detalle_factura_ibfk_1')->references(['id_factura'])->on('facturacion')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['id_producto'], 'detalle_factura_ibfk_3')->references(['id_producto'])->on('productos')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalle_factura', function (Blueprint $table) {
            $table->dropForeign('detalle_factura_ibfk_1');
            $table->dropForeign('detalle_factura_ibfk_3');
        });
    }
};
