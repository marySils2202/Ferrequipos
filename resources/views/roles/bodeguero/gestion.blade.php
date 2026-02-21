
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Gestión de Productos y Proveedores</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/ProveedoresYProductos.css') }}">
      <link rel="stylesheet" href="{{ asset('css/ProveedoresYProductos2.css') }}">
</head>
<body>
  <div id="alert-container" class="position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index:1050;"></div>

  <div class="container py-4">
     <a href="{{ route('productos') }}" class="back-corner" title="Volver">←</a>

    <div class="btn-group mb-4" role="group">
      <button type="button" class="btn btn-outline-primary" id="tabProductos">Productos</button>
      <button type="button" class="btn btn-outline-primary" id="tabProveedores">Proveedores</button>
    </div>
    <div class="section active" id="sectionProductos">
      <div class="card mb-5">
        <div class="card-header d-flex align-items-center">
          <h2 class="mb-0">📦 Gestor de Productos</h2>
        </div>
        <div class="card-body">
          <button id="toggleProductos" class="btn-toggle">
  ⇅ Mostrar todo
</button>
          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <input type="text" id="searchProducto" class="form-control" placeholder="Buscar producto...">
            </div>
            <div class="col-md-6">
              <select id="filterCategoria" class="form-select">
                <option value="">— Todas las categorías —</option>
                @foreach($categorias as $cat)
                  <option value="{{ $cat->id_categoria }}">{{ $cat->nombre_categoria }}</option>
                @endforeach
              </select>
            </div>
          </div>
    <div id="sectionProductos" class="section active">
<table class="table table-bordered table-striped" id="tableProductos">
  <thead class="table-light">
    <tr>
      <th>#</th>
      <th>Nombre</th>
      <th>Descripción</th>            
      <th>Categoría</th>
      <th>Estado</th>
      <th>Stock Mínimo</th>
      <th>Precio Venta</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    @foreach($productos as $prod)
      <tr
        data-id="{{ $prod->id_producto }}"
        data-nombre="{{ Str::lower($prod->nombre) }}"
        data-descripcion="{{ Str::lower($prod->descripcion ?? '') }}"
        data-categoria="{{ $prod->id_categoria }}"
        data-estado="{{ $prod->estado }}"
      >
        <td>{{ $loop->iteration }}</td>
        <td class="cell-nombre">{{ $prod->nombre }}</td>
        <td class="cell-descripcion">{{ $prod->descripcion ?? '' }}</td> 
        <td class="cell-cat">{{ $prod->categoria->nombre_categoria }}</td>
        <td class="cell-estado">{{ $prod->estado ? 'Activo' : 'Inactivo' }}</td>
        <td class="cell-stock">{{ $prod->stock_minimo }}</td>
        <td class="cell-precio">{{ number_format($prod->precio_venta,2) }}</td>
        <td class="text-center">
          <button class="btn btn-sm btn-outline-warning btn-edit">✏️</button>
          <button class="btn btn-sm btn-outline-success btn-save d-none">💾</button>
          <button class="btn btn-sm btn-outline-secondary btn-cancel d-none">❌</button>
          @if(!$prod->estado)
            <button class="btn btn-sm btn-outline-danger btn-delete">🗑️</button>
          @else
            <button class="btn btn-sm btn-outline-secondary" disabled title="Producto activo no puede eliminarse">🗑️</button>
          @endif
        </td>
      </tr>
    @endforeach
  </tbody>
</table>

          </div>
        </div>
      </div>
    </div>
   <div id="sectionProveedores" class="section">
      <div class="card">
        <div class="card-header">
          <h2 class="mb-0">🤝 Gestor de Proveedores</h2>
        </div>
        <div class="card-body">
                    <button id="toggleProveedores" class="btn-toggle">⇅ Mostrar todo</button>
          <input type="text" id="searchProveedor" class="form-control mb-3" placeholder="Buscar proveedor...">
          <div class="table-responsive">
            <table class="table table-bordered table-striped" id="tableProveedores">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Nombre</th>
                  <th>Domicilio</th>
                  <th>Teléfono</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                @foreach($proveedores as $prov)
                  <tr data-nombre="{{ Str::lower($prov->nombre) }}">
                    <td>{{ $loop->iteration }}</td>
                    <td class="prov-nombre">{{ $prov->nombre }}</td>
                    <td>{{ $prov->domicilio ?? '-' }}</td>
                    <td>{{ $prov->telefono ?? '-' }}</td>
                    <td class="text-center">
                      <button class="btn btn-sm btn-warning" data-bs-toggle="collapse" data-bs-target="#editProv-{{ $prov->id_proveedor }}">✏️</button>
                      @if($prov->compras()->count()==0)
                        <form action="{{ route('proveedores.destroy',$prov->id_proveedor) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este proveedor?')">
                          @csrf @method('DELETE')
                          <button class="btn btn-sm btn-danger">🗑️</button>
                        </form>
                      @else
                        <button class="btn btn-sm btn-secondary" disabled title="Tiene compras registradas">🗑️</button>
                      @endif
                    </td>
                  </tr>
                  <tr class="collapse" id="editProv-{{ $prov->id_proveedor }}">
                    <td colspan="5">
                      <form action="{{ route('proveedores.update',$prov->id_proveedor) }}" method="POST" class="row g-3 needs-validation" novalidate>
                        @csrf @method('PUT')
                        <div class="col-md-4">
                          <input name="nombre" class="form-control" value="{{ old('nombre',$prov->nombre) }}" required maxlength="100">
                          <div class="invalid-feedback">Requiere nombre.</div>
                        </div>
                        <div class="col-md-4">
                          <input name="domicilio" class="form-control" value="{{ old('domicilio',$prov->domicilio) }}" maxlength="150">
                        </div>
                        <div class="col-md-3">
                          <input name="telefono" class="form-control" value="{{ old('telefono',$prov->telefono) }}" maxlength="50">
                        </div>
                        <div class="col-12 text-end">
                          <button type="button" class="btn btn-secondary" data-bs-toggle="collapse" data-bs-target="#editProv-{{ $prov->id_proveedor }}">Cancelar</button>
                          <button type="submit" class="btn btn-warning">Actualizar</button>
                        </div>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
            <script>
  const categoriasOptionsHtml = `
    @foreach($categorias as $cat)
      <option value="{{ $cat->id_categoria }}">{{ $cat->nombre_categoria }}</option>
    @endforeach
  `;
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/Bodega/GestionDeDatos.js') }}"></script>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
