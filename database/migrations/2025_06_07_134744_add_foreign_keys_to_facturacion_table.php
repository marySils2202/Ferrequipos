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
        Schema::table('facturacion', function (Blueprint $table) {
            $table->foreign(['id_cliente'], 'facturacion_ibfk_1')->references(['id_cliente'])->on('clientes')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['id_usuario'], 'facturacion_ibfk_2')->references(['id_usuario'])->on('usuarios')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['mecanico_id'], 'fk_facturacion_mecanico')->references(['id_mecanico'])->on('mecanicos')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facturacion', function (Blueprint $table) {
            $table->dropForeign('facturacion_ibfk_1');
            $table->dropForeign('facturacion_ibfk_2');
            $table->dropForeign('fk_facturacion_mecanico');
        });
    }
};
