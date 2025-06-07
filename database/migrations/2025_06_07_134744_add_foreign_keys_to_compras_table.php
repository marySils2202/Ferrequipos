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
        Schema::table('compras', function (Blueprint $table) {
            $table->foreign(['id_proveedor'], 'compras_ibfk_1')->references(['id_proveedor'])->on('proveedores')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['id_usuario'], 'compras_ibfk_2')->references(['id_usuario'])->on('usuarios')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->dropForeign('compras_ibfk_1');
            $table->dropForeign('compras_ibfk_2');
        });
    }
};
