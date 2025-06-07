<?php
use App\Http\Livewire\Backups\DatabaseManager;
use App\Http\Controllers\Auth\ForgotPasswordController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Http\Controllers\{
   EstadisticasController,
    AuthController,
    UsuarioController,
    CompraController,
    ProductoController,
    ProveedorController,
    MovimientoController,
    ClienteController,
    InventarioController,
    FacturacionController,
    ArqueoController,
    FiltrosController,
    HomeController,
    LibroVentasController,
    CreditoController
};
Route::get('/', fn() => redirect()->route('home'));
Route::get('/home', [HomeController::class, 'index'])
     ->name('home');


Route::get('/api/me', [HomeController::class, 'me'])
     ->middleware('auth:sanctum');  
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

Route::post('/logout', [AuthController::class, 'logout'])
     ->middleware('auth')
     ->name('logout');


Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
     ->name('password.request');

Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
     ->name('password.email');


Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
     ->name('password.reset');

Route::post('password/reset', [ResetPasswordController::class, 'reset'])
     ->name('password.update');
     


Route::middleware('auth')->group(function () {


    Route::view('/sistema', 'sistema')->name('sistema');

    Route::view('/productos', 'productos')
          ->middleware(['role:admin,bodeguero'])
          ->name('productos');
    Route::delete(
    'creditos/{id}/reembolso',
    [CreditoController::class, 'reembolsoCredito']
)->name('creditos.reembolso');
Route::delete(
    'creditos/{id}/reembolso',
    [CreditoController::class, 'reembolsoCredito']
)->name('creditos.reembolso');

          // Sólo necesitamos store y destroy
Route::post  ('/mecanicos',           [App\Http\Controllers\MecanicoController::class, 'store'])   ->name('mecanicos.store');
Route::delete('/mecanicos/{mecanico}',[App\Http\Controllers\MecanicoController::class, 'destroy'])->name('mecanicos.destroy');

    Route::view('/factura', 'factura')->name('factura')
          ->middleware(['role:admin,facturador']);
    

    Route::view('/arqueo', 'arqueo')->name('arqueo')
          ->middleware(['role:admin,facturador']);
    
    Route::view('/panel', 'panel')->name('vistas.panel')
          ->middleware(['role:admin,bodeguero']);
    


    Route::get('/notificaciones', [\App\Http\Controllers\NotificacionesController::class, 'index'])
         ->name('notificaciones');

  
    Route::get('/inventario', [InventarioController::class, 'index'])
         ->name('inventario')
               ->middleware(['role:admin,bodeguero']);
    Route::post('/inventario', [InventarioController::class, 'actualizarInventario'])
         ->name('inventario.update');


    Route::get('/clientes',   [ClienteController::class,'index'])  ->name('clientes.index')
          ->middleware(['role:admin,facturador']);
    Route::post('/clientes',  [ClienteController::class,'store'])  ->name('clientes.store');
    Route::put('/clientes/{cliente}', [ClienteController::class,'update'])->name('clientes.update');
    Route::delete('/clientes/{cliente}', [ClienteController::class,'destroy'])->name('clientes.destroy');



    Route::middleware('isAdmin')->group(function () {

Route::get('filtros', [FiltrosController::class, 'index'])->name('filtros.index')
      ->middleware(['role:admin'])
            ->middleware(['role:admin']);

Route::get('filtros/export-pdf', [FiltrosController::class, 'exportPdf'])->name('filtros.exportPdf');

Route::get('compras/detalle', [CompraController::class, 'detalleCompras'])
     ->name('compras.detalleCompras');

 


    
        Route::resource('personal', UsuarioController::class)
             ->only(['index','store','update','destroy'])
                   ->middleware(['role:admin'])
             ->names([
                'index'=>'personal.index',
                'store'=>'personal.store',
                'update'=>'personal.update',
                'destroy'=>'personal.destroy',
             ])
                   ->middleware(['role:admin']);
    });


   
    Route::get('/vistas/agregar-producto', [ProductoController::class, 'create'])
         ->name('vistas.agregar_producto');
    Route::get('/vistas/agregar-proveedor', fn() => view('agregar_proveedor'))
         ->name('vistas.agregar_proveedor');

    Route::get('/gestor-productos', fn() => view('gestor_productos'))
         ->name('vistas.gestor_productos')
               ->middleware(['role:admin,bodeguero']);



Route::post('movimientos', [MovimientoController::class, 'store'])
     ->name('movimientos.store');

Route::get('movimientos/pdf', [MovimientoController::class, 'exportPdf'])
     ->name('movimientos.pdf');


     Route::get('movimientos/stats-pdf', [MovimientoController::class, 'exportStatsPdf'])
     ->name('movimientos.statsPdf');



    Route::post('/factura', [FacturacionController::class,'store'])
         ->name('facturacion.store');



    Route::post('/productos',               [ProductoController::class, 'store'])
         ->name('productos.store');
    Route::put('/productos/{producto}',     [ProductoController::class, 'update'])
         ->name('productos.update');
    Route::delete('/productos/{producto}',  [ProductoController::class, 'destroy'])
         ->name('productos.destroy');

    Route::get('/agregar-proveedor', [ProveedorController::class, 'index'])
         ->name('vistas.agregar_proveedor');
    Route::post('/proveedores', [ProveedorController::class, 'store'])
         ->name('proveedores.store');
    Route::put('/proveedores/{proveedor}', [ProveedorController::class, 'update'])
         ->name('proveedores.update');
    Route::delete('/proveedores/{proveedor}', [ProveedorController::class, 'destroy'])
         ->name('proveedores.destroy');



Route::post('arqueo', [ArqueoController::class, 'store'])
     ->name('arqueo.store');


Route::post('arqueo/{id}/cerrar', [ArqueoController::class, 'cerrar'])
     ->name('arqueo.cerrar');

Route::get('arqueo/{id}/edit', [ArqueoController::class, 'edit'])
     ->name('arqueo.edit');

Route::put('arqueo/{id}', [ArqueoController::class, 'update'])
     ->name('arqueo.update');

Route::get('arqueo/pdf', [ArqueoController::class, 'pdf'])
     ->name('arqueo.pdf');


Route::get('arqueo/{id}/print', [ArqueoController::class, 'print'])
     ->name('arqueo.print');
});


     Route::get('arqueo', [ArqueoController::class, 'index'])
     ->name('arqueo.index')
           ->middleware(['role:admin,facturador']);


Route::get('movimientos', [MovimientoController::class, 'index'])
     ->name('movimientos.index')
           ->middleware(['role:admin'])
     ->middleware('auth');  


Route::get('/factura',  [FacturacionController::class,'index'])
         ->name('factura')
               ->middleware(['role:admin,facturador'])
              ->middleware('auth');
             


Route::get('/libro-ventas', [LibroVentasController::class, 'index'])
     ->name('libro-ventas.index')
           ->middleware(['role:admin,facturador'])
     ->middleware('auth');



Route::get('/estadisticas', [EstadisticasController::class, 'index'])
     ->name('estadisticas.index')
     ->middleware('auth')
           ->middleware(['role:admin']);

Route::get('inventario/pdf', [InventarioController::class, 'exportPdf'])
     ->name('inventario.pdf');
 Route::get('/compras', [CompraController::class, 'create'])
         ->name('compras.create');

    Route::post('/compras', [CompraController::class, 'store'])
         ->name('compras.store');


    Route::get('/compras/detalle', [CompraController::class, 'detalleCompras'])
         ->name('compras.detalle');

    Route::get('/vistas/compras',          [CompraController::class, 'create'])
         ->name('vistas.compras');
    Route::get('/vistas/detalle-compras',  [CompraController::class, 'detalleCompras'])
         ->name('vistas.detalle_compras');
         Route::get(
    '/vistas/agregar-producto',
    [ProductoController::class, 'index']
)->name('vistas.agregar_producto');


Route::post(
    '/productos',
    [ProductoController::class, 'store']
)->name('productos.store');


Route::get(
    '/vistas/editar-producto/{producto}',
    [ProductoController::class, 'edit']
)->name('vistas.editar_producto');


Route::put(
    '/productos/{producto}',
    [ProductoController::class, 'update']
)->name('productos.update');


Route::delete(
    '/productos/{producto}',
    [ProductoController::class, 'destroy']
)->name('productos.destroy');

Route::get('/movimientos/pdf', [MovimientoController::class, 'exportPdf'])
     ->name('movimientos.pdf');
     
     Route::get('facturacion/{id}/print', [FacturacionController::class, 'print'])
     ->name('facturacion.print');
Route::get('/gestion', function () {
    $categorias   = Categoria::all();
    $productos    = Producto::with('categoria')->get();
    $proveedores  = Proveedor::all();
    return view('gestion', compact('categorias', 'productos', 'proveedores'))
    ;



})->name('gestion')
      ->middleware(['role:admin,bodeguero']);
Route::get('/producto/{id}/json', function($id) {
  $prod = Producto::findOrFail($id);
  return response()->json(['descripcion' => $prod->descripcion]);
  
});

Route::get('facturacion/{id}/print', [FacturacionController::class, 'print'])
     ->name('facturacion.print');

Route::get('facturacion/{id}/reprint', [FacturacionController::class, 'reprint'])
     ->name('facturacion.reprint');
Route::get('facturacion/audit', [FacturacionController::class, 'audit'])
     ->name('facturacion.audit');
Route::middleware(['auth', 'can:admin'])->group(function() {

});
Route::middleware(['auth'])->group(function () {
    Route::get('creditos',           [CreditoController::class, 'index'])->name('creditos.index')
          ->middleware(['role:admin,facturador']);
    Route::get('creditos/{id}',      [CreditoController::class, 'show'])->name('creditos.show');
    Route::post('creditos/{id}/abonar',[CreditoController::class, 'abonar'])->name('creditos.abonar');
});
Route::middleware('auth')->group(function () {
    Route::get('creditos',            [CreditoController::class, 'index'])->name('creditos.index');
    Route::get('creditos/{id}',       [CreditoController::class, 'show']) ->name('creditos.show');
    Route::post('creditos/{id}/abonar',[CreditoController::class, 'abonar'])->name('creditos.abonar');
    
});
Route::middleware('auth')->group(function () {
    /* creación de crédito (carrito) */
    Route::post('creditos', [CreditoController::class,'store'])->name('creditos.store');

    /* abonos y cancelar */
    Route::post  ('creditos/{id}/abonar', [CreditoController::class,'abonar']) ->name('creditos.abonar');
    Route::delete('creditos/{id}',        [CreditoController::class,'cancelar'])->name('creditos.cancelar');

    /* listado y detalle */
    Route::get('creditos',      [CreditoController::class,'index'])->name('creditos.index')
          ->middleware(['role:admin,facturador']);
    Route::get('creditos/{id}', [CreditoController::class,'show'])->name('creditos.show');

 Route::get('facturacion/{id}/print', [FacturacionController::class, 'print'])
     ->name('facturacion.print');
});Route::get('facturacion/pago-mecanico', [CreditoController::class, 'pagoMecanico'])
     ->name('facturacion.pago_mecanico')
     ;Route::get('facturacion/pago-mecanico', [FacturacionController::class, 'pagoMecanico'])
   ->middleware(['role:admin,facturador'])
     ->name('facturacion.pago_mecanico');
Route::get('facturacion/recibo-pdf', [FacturacionController::class, 'reciboPdf'])
     ->name('facturacion.recibo.pdf');
     
Route::get('/backup-manager', function () {
    return view('backup-manager');
})->name('backup.manager')
  ->middleware(['role:admin']);;