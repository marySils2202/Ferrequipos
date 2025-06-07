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
        Schema::create('compras', function (Blueprint $table) {
            $table->integer('id_compra', true);
            $table->integer('id_proveedor')->index('id_proveedor');
            $table->integer('id_usuario')->index('id_usuario');
            $table->timestamp('fecha')->nullable()->useCurrent();
            $table->integer('cantidad');
            $table->decimal('total', 10);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
