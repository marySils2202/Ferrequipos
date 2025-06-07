<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArqueoSeeder extends Seeder
{
    public function run()
    {
        DB::table('arqueo')->insert([
            'id_arqueo'    => 1,
            'fecha'         => '2025-06-05 19:54:15',
            'monto_inicial' => 9256.00,
            'monto_final'   => null,
            'salida_caja'   => 0.00,
            'razon_salida'  => null,
            'printed_by'    => null,
            'printed_at'    => null,
        ]);
    }
}
