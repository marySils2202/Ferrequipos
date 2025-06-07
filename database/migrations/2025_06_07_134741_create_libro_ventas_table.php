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
        Schema::create('libro_ventas', function (Blueprint $table) {
            $table->integer('id_libro_venta', true);
            $table->date('fecha');
            $table->decimal('monto_final', 10);
            $table->integer('id_arqueo')->nullable()->index('fk_libro_arqueo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('libro_ventas');
    }
};
