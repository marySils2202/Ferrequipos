<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reportes Administrativos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/Reportes.css') }}">
</head>
<body>

  <div class="report-card">
    <a href="{{ route('sistema') }}" class="back-corner" title="Volver">←</a>
    <h2>🔧 Panel de Reportes</h2>

    <form method="GET" action="{{ route('filtros.index') }}">
      <div class="filter-bar d-flex flex-wrap gap-2 align-items-end">
        <div>
          <label class="form-label">Tipo de reporte</label>
          <select name="tipo_reporte" id="tipoReporte" class="form-select" required>
            <option value="" disabled {{ $tipo_reporte=='' ? 'selected' : '' }}>— Seleccione —</option>
            <option value="clientes"    @selected($tipo_reporte=='clientes')>Clientes</option>
            <option value="usuarios"    @selected($tipo_reporte=='usuarios')>Usuarios</option>
            <option value="proveedores" @selected($tipo_reporte=='proveedores')>Proveedores</option>
            <option value="facturas"    @selected($tipo_reporte=='facturas')>Facturas</option>
            <option value="compras"     @selected($tipo_reporte=='compras')>Compras</option>
            <option value="productos"   @selected($tipo_reporte=='productos')>Productos</option>
            <option value="creditos"    @selected($tipo_reporte=='creditos')>Créditos</option>
          </select>
        </div>
        <div>
          <label class="form-label">Fecha</label>
          <input
            type="date"
            name="fecha"
            id="fechaFiltro"
            value="{{ $fecha }}"
            class="form-control {{ !in_array($tipo_reporte, ['facturas','compras']) ? 'disabled' : '' }}"
            {{ !in_array($tipo_reporte, ['facturas','compras']) ? 'disabled' : 'required' }}
          >
        </div>
        <div id="filtrosCreditos" class="{{ $tipo_reporte==='creditos' ? '' : 'd-none' }}">
          <label class="form-label">Cliente (parcial)</label>
          <input
            type="text"
            name="cliente_nombre"
            value="{{ request('cliente_nombre') }}"
            class="form-control"
            placeholder="Ej. López"
          >
          <label class="form-label mt-2">Estado</label>
          <select name="credito_estado" class="form-select">
            <option value=""        @selected(request('credito_estado')=='')>Todos</option>
            <option value="pendiente" @selected(request('credito_estado')=='pendiente')>Pendiente</option>
            <option value="pagado"    @selected(request('credito_estado')=='pagado')>Pagado</option>
          </select>
        </div>
        <div class="mt-4 d-flex gap-2">
          <button type="submit" class="btn btn-primary">Mostrar</button>
          <button type="button" id="btnClear" class="btn btn-outline-secondary">Limpiar</button>
          <a
            @if($tipo_reporte)
              href="{{ route('filtros.exportPdf', request()->only(
                'tipo_reporte','fecha','cliente_nombre','credito_estado'
              )) }}"
            @endif
            class="btn-print btn btn-outline-success {{ $tipo_reporte=='' ? 'disabled' : '' }}"
            @if(!$tipo_reporte)
              aria-disabled="true" tabindex="-1"
            @endif
          >
            📄 Descargar PDF
          </a>
        </div>
      </div>
    </form>

    <div class="results mt-4">
      @switch($tipo_reporte)
        @case('clientes')
          <h3>📋 Clientes</h3>
          @include('filtros.partials.tabla_clientes',    ['clientes'   => $clientes])
          @break

        @case('usuarios')
          <h3>👤 Usuarios</h3>
          @include('filtros.partials.tabla_usuarios',    ['usuarios'   => $usuarios])
          @break

        @case('proveedores')
          <h3>🚚 Proveedores</h3>
          @include('filtros.partials.tabla_proveedores',['proveedores'=> $proveedores])
          @break

        @case('facturas')
          <h3>🧾 Facturas en {{ $fecha }}</h3>
          @include('filtros.partials.tabla_facturas',    ['facturas'   => $facturas])
          @break

        @case('compras')
          <h3>🛒 Compras en {{ $fecha }}</h3>
          @include('filtros.partials.tabla_compras',     ['compras'    => $compras])
          @break

        @case('productos')
          <h3>📦 Productos</h3>
          @include('filtros.partials.tabla_productos',   ['productos'  => $productos])
          @break

        @case('creditos')
          <h3>💳 Créditos</h3>
          @include('filtros.partials.tabla_creditos',    ['creditos'   => $creditos])
          @break

        @default
          <p class="text-muted text-center mt-5">
            Selecciona un tipo de reporte y haz clic en “Mostrar”.
          </p>
      @endswitch
    </div>
    
  </div>
   <script src="{{ asset('js/Mantenimiento/Reportes.js') }}"></script>
</body>
</html>
