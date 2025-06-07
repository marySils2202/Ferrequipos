<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Producto</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/ProductosPanel.css') }}">
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
</head>
<body>
<div class="panel-container">
  <a href="{{ route('sistema') }}" class="back-corner" title="Volver al Sistema">←</a>
  <img src="{{ asset('imagenes/logo_quinteros.png') }}" class="panel-logo" alt="Logo">
  <h2>Gestión de Producto</h2>
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  <nav class="d-flex flex-wrap justify-content-center gap-3 mb-4">
    <button class="btn btn-modal" data-ruta="{{ route('vistas.compras') }}">📦 Registrar Compra</button>
    <button class="btn btn-modal" data-ruta="{{ route('vistas.agregar_producto') }}">📋 Gestor de Productos</button>
    <button class="btn btn-modal" data-ruta="{{ route('vistas.agregar_proveedor') }}">📋 Gestor de Proveedores</button>
    <a href="{{ route('inventario') }}" class="btn">📊 Inventario</a>
    <a href="{{ route('gestion') }}" class="fab-pen" title="Gestor de Datos">✏️</a>
  </nav>

  <div class="modal fade" id="modalMain" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 id="modalTitle" class="modal-title fw-semibold"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div id="modalBody" class="modal-body text-center"></div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
  <script src="{{ asset('js/Bodega/PanelProductos.js') }}"></script>
</body>
</html>
