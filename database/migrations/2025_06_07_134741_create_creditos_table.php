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
        Schema::create('creditos', function (Blueprint $table) {
            $table->integer('id_credito', true);
            $table->integer('id_factura')->index('fk_credito_factura');
            $table->decimal('monto_total', 10);
            $table->decimal('monto_pagado', 10)->default(0);
            $table->decimal('saldo', 10)->nullable()->storedAs('(`monto_total` - `monto_pagado`)');
            $table->dateTime('fecha_inicio')->useCurrent();
            $table->enum('estado', ['pendiente', 'parcial', 'pagado'])->default('pendiente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creditos');
    }
};
