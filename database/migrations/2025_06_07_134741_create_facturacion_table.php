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
        Schema::create('facturacion', function (Blueprint $table) {
            $table->integer('id_factura', true);
            $table->integer('id_cliente')->index('id_cliente');
            $table->integer('id_usuario')->index('id_usuario');
            $table->unsignedInteger('mecanico_id')->nullable()->index('fk_facturacion_mecanico');
            $table->timestamp('fecha')->nullable()->useCurrent();
            $table->decimal('total', 10);
            $table->decimal('monto_pago', 10);
            $table->decimal('vuelto', 10);
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'credito'])->default('efectivo');
            $table->decimal('saldo_pendiente', 10)->default(0);
            $table->decimal('mano_obra', 10)->default(0);
            $table->decimal('descuento', 10)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturacion');
    }
};
