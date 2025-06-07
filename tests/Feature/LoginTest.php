<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function usuario_con_credenciales_validas_puede_ingresar()
    {
        // Factory: crea un usuario en BD de testing
        $user = Usuario::factory()->create([
            'password' => bcrypt($password = 'secreto123')
        ]);

        $response = $this->post('/login', [
            'username' => $user->username,
            'password' => $password,
        ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
    }
}
