<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registrar Compra</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/Compras.css') }}">
  
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

  <div class="container py-4">
    <div id="compraAlert">
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif
    </div>

    <form id="formRegistrarCompra" action="{{ route('compras.store') }}" method="POST" novalidate>
      @csrf
      <h4 class="mb-4 text-primary">📦 Registrar Compra</h4>
      <div class="mb-3">
        <label for="id_proveedor" class="form-label">Proveedor:</label>
        <select name="id_proveedor" id="id_proveedor" class="form-select @error('id_proveedor') is-invalid @enderror" required>
          <option value="">-- Selecciona --</option>
          @foreach($proveedores as $p)
            <option value="{{ $p->id_proveedor }}"
              {{ old('id_proveedor') == $p->id_proveedor ? 'selected' : '' }}>
              {{ $p->nombre }}
            </option>
          @endforeach
        </select>
        @error('id_proveedor')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="mb-3">
        <label class="form-label">Usuario:</label>
        <input type="text" class="form-control" value="{{ $usuario->nombre }}" disabled>
        <input type="hidden" name="id_usuario" value="{{ $usuario->id_usuario }}">
      </div>
      <div class="mb-3">
        <label for="id_producto" class="form-label">Producto:</label>
        <select name="id_producto" id="id_producto" class="form-select @error('id_producto') is-invalid @enderror" required>
          <option value="">-- Selecciona --</option>
          @foreach($productos as $prod)
            <option
              value="{{ $prod->id_producto }}"
              data-precio="{{ $prod->precio_venta }}"
              data-descripcion="{{ $prod->descripcion }}"
              {{ old('id_producto') == $prod->id_producto ? 'selected' : '' }}
            >
              {{ $prod->nombre }}
            </option>
          @endforeach
        </select>
        @error('id_producto')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="mb-3">
        <label for="descripcion_producto" class="form-label">Descripción del Producto:</label>
        <textarea id="descripcion_producto"
                  class="form-control"
                  rows="2"
                  readonly>{{ old('descripcion_producto') }}</textarea>
      </div>
      <div class="mb-3">
        <label for="cantidad" class="form-label">Cantidad:</label>
        <input type="number"
               name="cantidad"
               id="cantidad"
               class="form-control @error('cantidad') is-invalid @enderror"
               min="1"
               value="{{ old('cantidad') }}"
               required>
        @error('cantidad')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="mb-3">
        <label for="precio_unitario" class="form-label">Precio unitario (C$):</label>
        <input type="number"
               name="precio_unitario"
               id="precio_unitario"
               class="form-control @error('precio_unitario') is-invalid @enderror"
               step="0.01"
               min="0"
               value="{{ old('precio_unitario') }}"
               required>
        <div class="form-text">
          No puede ser mayor al precio de venta del producto.
        </div>
        @error('precio_unitario')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <button type="submit" class="btn btn-success w-100" id="btnGuardar" disabled>
        Guardar Compra
      </button>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
 <script src="{{ asset('js/Bodega/Compras.js') }}"></script>
</body>
</html>
