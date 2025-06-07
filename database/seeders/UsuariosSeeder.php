<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    public function run()
    {
        DB::table('usuarios')->insert([
            'id_usuario'     => 1,
            'username'       => 'Admin',           
            'password'       => Hash::make('SpAdmin123'),
            'remember_token' => null,
            'nombre'         => 'Admin GOD',
            'email'          => 'Exemple@gmil.com',
            'rol'            => 'admin',
        ]);
    }
}
