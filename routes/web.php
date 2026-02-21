<?php

use App\Http\Controllers\ArqueoController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\CreditoController;
use App\Http\Controllers\EstadisticasController;
use App\Http\Controllers\FacturacionController;
use App\Http\Controllers\FiltrosController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\LibroVentasController;
use App\Http\Controllers\MecanicoController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\NotificacionesController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\UsuarioController;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('home'));
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/api/me', [HomeController::class, 'me'])->middleware('auth:sanctum');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::middleware('auth')->group(function () {
    Route::view('/sistema', 'roles.admin.sistema')->name('sistema');

    Route::post('/mecanicos', [MecanicoController::class, 'store'])->name('mecanicos.store');
    Route::delete('/mecanicos/{mecanico}', [MecanicoController::class, 'destroy'])->name('mecanicos.destroy');

    Route::post('movimientos', [MovimientoController::class, 'store'])->name('movimientos.store');
    Route::get('movimientos/pdf', [MovimientoController::class, 'exportPdf'])->name('movimientos.pdf');
    Route::get('movimientos/stats-pdf', [MovimientoController::class, 'exportStatsPdf'])->name('movimientos.statsPdf');

    Route::post('/factura', [FacturacionController::class, 'store'])->name('facturacion.store');
    Route::get('facturacion/{id}/print', [FacturacionController::class, 'print'])->name('facturacion.print');
    Route::get('facturacion/{id}/reprint', [FacturacionController::class, 'reprint'])->name('facturacion.reprint');
    Route::get('facturacion/audit', [FacturacionController::class, 'audit'])->name('facturacion.audit');
    Route::get('facturacion/pago-mecanico', [FacturacionController::class, 'pagoMecanico'])
        ->name('facturacion.pago_mecanico')
        ->middleware(['role:admin,facturador']);
    Route::get('facturacion/recibo-pdf', [FacturacionController::class, 'reciboPdf'])->name('facturacion.recibo.pdf');

    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
    Route::get('/producto/{id}/json', function ($id) {
        $prod = Producto::findOrFail($id);
        return response()->json(['descripcion' => $prod->descripcion]);
    });

    Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
    Route::put('/proveedores/{proveedor}', [ProveedorController::class, 'update'])->name('proveedores.update');
    Route::delete('/proveedores/{proveedor}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');

    Route::post('arqueo', [ArqueoController::class, 'store'])->name('arqueo.store');
    Route::post('arqueo/{id}/cerrar', [ArqueoController::class, 'cerrar'])->name('arqueo.cerrar');
    Route::get('arqueo/{id}/edit', [ArqueoController::class, 'edit'])->name('arqueo.edit');
    Route::put('arqueo/{id}', [ArqueoController::class, 'update'])->name('arqueo.update');
    Route::get('arqueo/pdf', [ArqueoController::class, 'pdf'])->name('arqueo.pdf');
    Route::get('arqueo/{id}/print', [ArqueoController::class, 'print'])->name('arqueo.print');

    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');

    Route::post('/compras', [CompraController::class, 'store'])->name('compras.store');

    Route::post('creditos', [CreditoController::class, 'store'])->name('creditos.store');
    Route::post('creditos/{id}/abonar', [CreditoController::class, 'abonar'])->name('creditos.abonar');
    Route::delete('creditos/{id}', [CreditoController::class, 'cancelar'])->name('creditos.cancelar');
    Route::delete('creditos/{id}/reembolso', [CreditoController::class, 'reembolsoCredito'])->name('creditos.reembolso');
});

Route::middleware(['auth', 'role:admin,bodeguero'])->group(function () {
    Route::view('/productos', 'roles.bodeguero.productos')->name('productos');
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario');
    Route::post('/inventario', [InventarioController::class, 'actualizarInventario'])->name('inventario.update');
    Route::get('inventario/pdf', [InventarioController::class, 'exportPdf'])->name('inventario.pdf');

    Route::get('/vistas/agregar-producto', [ProductoController::class, 'create'])->name('vistas.agregar_producto');
    Route::get('/vistas/agregar-proveedor', [ProveedorController::class, 'index'])->name('vistas.agregar_proveedor');
    Route::get('/vistas/compras', [CompraController::class, 'create'])->name('vistas.compras');
    Route::get('/vistas/detalle-compras', [CompraController::class, 'detalleCompras'])->name('vistas.detalle_compras');

    Route::get('/compras', [CompraController::class, 'create'])->name('compras.create');
    Route::get('/compras/detalle', [CompraController::class, 'detalleCompras'])->name('compras.detalle');

    Route::get('/gestion', function () {
        $categorias = Categoria::all();
        $productos = Producto::with('categoria')->get();
        $proveedores = Proveedor::all();

        return view('roles.bodeguero.gestion', compact('categorias', 'productos', 'proveedores'));
    })->name('gestion');
});

Route::middleware(['auth', 'role:admin,facturador'])->group(function () {
    Route::get('/factura', [FacturacionController::class, 'index'])->name('factura');
    Route::get('/arqueo', [ArqueoController::class, 'index'])->name('arqueo.index');
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/libro-ventas', [LibroVentasController::class, 'index'])->name('libro-ventas.index');
    Route::get('/creditos', [CreditoController::class, 'index'])->name('creditos.index');
    Route::get('/creditos/{id}', [CreditoController::class, 'show'])->name('creditos.show');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/movimientos', [MovimientoController::class, 'index'])->name('movimientos.index');
    Route::get('/estadisticas', [EstadisticasController::class, 'index'])->name('estadisticas.index');
    Route::get('/notificaciones', [NotificacionesController::class, 'index'])->name('notificaciones');
    Route::get('/filtros', [FiltrosController::class, 'index'])->name('filtros.index');
    Route::get('/filtros/export-pdf', [FiltrosController::class, 'exportPdf'])->name('filtros.exportPdf');

    Route::resource('personal', UsuarioController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->names([
            'index' => 'personal.index',
            'store' => 'personal.store',
            'update' => 'personal.update',
            'destroy' => 'personal.destroy',
        ]);

    Route::view('/backup-manager', 'roles.admin.backup-manager')->name('backup.manager');
});
