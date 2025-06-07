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
        Schema::create('productos', function (Blueprint $table) {
            $table->integer('id_producto', true);
            $table->string('nombre', 100);
            $table->string('descripcion')->nullable();
            $table->decimal('precio_venta', 10)->nullable();
            $table->integer('stock_minimo')->default(0);
            $table->integer('id_categoria')->index('id_categoria');
            $table->tinyInteger('estado')->nullable()->default(1);
            $table->dateTime('deleted_at')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
