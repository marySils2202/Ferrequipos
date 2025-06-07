<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class UsuarioTest extends TestCase
{
    /** @test */
    public function puede_crear_un_usuario_en_memoria()
    {
        $user = new Usuario([
            'username' => 'juan',
            'email'    => 'juan@example.com',
            'password' => Hash::make('secreto'),
        ]);

        $this->assertEquals('juan', $user->username);
        $this->assertTrue(Hash::check('secreto', $user->password));
    }
}
