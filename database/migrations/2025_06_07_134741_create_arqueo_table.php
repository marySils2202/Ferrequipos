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
        Schema::create('arqueo', function (Blueprint $table) {
            $table->integer('id_arqueo', true);
            $table->dateTime('fecha')->useCurrent();
            $table->decimal('monto_inicial', 10);
            $table->decimal('monto_final', 10)->nullable();
            $table->decimal('salida_caja', 10)->default(0);
            $table->string('razon_salida')->nullable();
            $table->integer('printed_by')->nullable()->index('fk_arqueo_user');
            $table->dateTime('printed_at')->nullable();
            $table->decimal('diferencia', 10)->nullable()->storedAs('((coalesce(`monto_final`,0) - `monto_inicial`) - `salida_caja`)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arqueo');
    }
};
