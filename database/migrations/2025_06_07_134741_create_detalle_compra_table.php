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
        Schema::create('detalle_compra', function (Blueprint $table) {
            $table->integer('id_detalle', true);
            $table->integer('id_compra')->index('id_compra');
            $table->integer('id_producto')->nullable()->index('id_producto');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10);
            $table->decimal('subtotal', 10)->nullable()->storedAs('(`cantidad` * `precio_unitario`)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_compra');
    }
};
