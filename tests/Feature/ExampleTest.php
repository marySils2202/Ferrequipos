<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /** @test */
    public function la_aplicacion_redirige_a_login_desde_la_ruta_root()
    {
        $response = $this->get('/');

        // Antes esperabas 200, ahora validamos que redirija a /login
        $response->assertRedirect('/login');
    }
}
