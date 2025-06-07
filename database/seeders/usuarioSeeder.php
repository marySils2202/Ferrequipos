<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Hash;



class usuarioSeeder extends Seeder
{

    public function run(): void
    {
        Usuario::created([
            'username' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin1234'),
            'nombre' => 'Juannnn',
            'role' => 'admin',
        ]);
    }
}
