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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->integer('id_usuario', true);
            $table->string('username', 50)->unique('username');
            $table->string('password');
            $table->rememberToken();
            $table->string('nombre', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->enum('rol', ['admin', 'facturador', 'bodeguero']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
