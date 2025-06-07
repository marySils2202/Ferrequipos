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
        Schema::table('arqueo', function (Blueprint $table) {
            $table->foreign(['printed_by'], 'fk_arqueo_user')->references(['id_usuario'])->on('usuarios')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arqueo', function (Blueprint $table) {
            $table->dropForeign('fk_arqueo_user');
        });
    }
};
