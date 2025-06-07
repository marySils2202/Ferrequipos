<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $payload;

    protected function setUp(): void
    {
        parent::setUp();

        // 1) Creamos la categoría con ID = 1
        Categoria::create(['nombre_categoria' => 'TestCat']);

        // 2) Creamos y autenticamos un admin
        $admin = Usuario::create([
            'username' => 'admin_test',
            'email'    => 'admin@test.com',
            'nombre'   => 'Admin Test',
            'password' => Hash::make('secret123'),
            'rol'      => 'admin',
        ]);
        $this->actingAs($admin);

        // 3) Payload base con todos los campos requeridos
        $this->payload = [
            'nombre'        => 'Filtro Aceite',
            'descripcion'   => 'Alta calidad',
            'precio_venta'  => 75,
            'stock_minimo'  => 10,
            'id_categoria'  => 1,
            'estado'        => 1,
        ];
    }

    /** @test */
    public function admin_puede_crear_un_producto()
    {
        $response = $this->post('/productos', $this->payload);

        // Debe redirigir al índice
        $response->assertRedirect('/productos');

        // Y debe existir en la BD
        $this->assertDatabaseHas('productos', [
            'nombre'       => 'Filtro Aceite',
            'precio_venta' => 75,
        ]);
    }

    /** @test */
    public function admin_puede_actualizar_un_producto()
    {
        // 1) Primero creamos por HTTP
        $this->post('/productos', $this->payload);
        $prod = Producto::first();

        // 2) Modificamos precio y stock
        $update = array_merge($this->payload, [
            'precio_venta' => 120,
            'stock_minimo' => 5,
        ]);

        $response = $this->put("/productos/{$prod->id_producto}", $update);

        $response->assertRedirect('/productos');

        $this->assertDatabaseHas('productos', [
            'id_producto'  => $prod->id_producto,
            'precio_venta' => 120,
            'stock_minimo' => 5,
        ]);
    }

    /** @test */
    public function admin_puede_eliminar_un_producto()
    {
        // 1) Creamos por HTTP
        $this->post('/productos', $this->payload);
        $prod = Producto::first();

        // 2) Eliminamos
        $response = $this->delete("/productos/{$prod->id_producto}");

        $response->assertRedirect('/productos');

        $this->assertDatabaseMissing('productos', [
            'id_producto' => $prod->id_producto,
        ]);
    }
}
