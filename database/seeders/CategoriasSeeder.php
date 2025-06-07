<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriasSeeder extends Seeder
{
    public function run()
    {
        DB::table('categorias')->insert([
            ['id_categoria'    => 1,  'nombre_categoria' => 'Interna'],
            ['id_categoria'    => 2,  'nombre_categoria' => 'Externa'],
            ['id_categoria'    => 3,  'nombre_categoria' => 'Accesorios'],
            ['id_categoria'    => 4,  'nombre_categoria' => 'Lubricantes'],
            ['id_categoria'    => 5,  'nombre_categoria' => 'Tuercas/Tornillos'],
            ['id_categoria'    => 6,  'nombre_categoria' => 'Paquete'],
            ['id_categoria'    => 7,  'nombre_categoria' => 'Eléctrica'],
            ['id_categoria'    => 8,  'nombre_categoria' => 'Unidad'],
            ['id_categoria'    => 9,  'nombre_categoria' => 'Sprays'],
            ['id_categoria'    => 10, 'nombre_categoria' => 'Empaques'],
            ['id_categoria'    => 11, 'nombre_categoria' => 'Filtros'],
        ]);
    }
}
