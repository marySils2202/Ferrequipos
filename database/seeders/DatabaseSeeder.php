<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
   public function run()
{
 
    $this->call([
        ArqueoSeeder::class,
        CategoriasSeeder::class,
      UsuariosSeeder::class,
    ]);
}
}
