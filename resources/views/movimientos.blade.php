<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Histórico de Movimientos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/MovHistorico.css') }}">
</head>
<body>

  <a href="{{ route('sistema') }}" class="back-corner" title="Volver">←</a>
  <div class="movements-card">
    <h2>Estadisticas de Compras</h2>

    <form method="GET" action="{{ route('movimientos.index') }}" class="controls mb-4">
      <input type="text" name="nombre_producto" class="form-control"
             placeholder="🔍 Nombre de producto…" value="{{ request('nombre_producto') }}">
      <select name="categoria_id" class="form-control">
        <option value="">— Todas las categorías —</option>
        @foreach($categorias as $id => $cat)
          <option value="{{ $id }}" @selected(request('categoria_id')==$id)>{{ $cat }}</option>
        @endforeach
      </select>
      <button type="submit" class="btn btn-primary">Filtrar</button>
    </form>

    <button id="toggleStats" class="btn btn-outline-primary mb-3">
      👁️ Mostrar estadísticas
    </button>

    <div id="statsSection" class="collapse mb-5">
      @if($statsComprasPorProducto->isNotEmpty())
        <h3 class="text-primary mb-3">
          📦 Estadísticas de Compras
          @if(request('nombre_producto')) para “{{ request('nombre_producto') }}” @endif
          @if(request('categoria_id')) en {{ $categorias[request('categoria_id')] }} @endif
        </h3>
        <div class="table-responsive">
          <table class="table table-striped table-bordered align-middle text-center">
            <thead class="table-primary">
              <tr>
                <th>Producto</th>
                <th>Mínimo</th>
                <th>Máximo</th>
                <th>Promedio</th>
                <th># Compras</th>
              </tr>
            </thead>
            <tbody>
              @foreach($statsComprasPorProducto as $stat)
                <tr>
                  <td class="fw-bold">{{ $stat->producto->nombre }}</td>
                  <td>C${{ number_format($stat->min_precio,2) }}</td>
                  <td>C${{ number_format($stat->max_precio,2) }}</td>
                  <td>C${{ number_format($stat->avg_precio,2) }}</td>
                  <td>{{ $stat->num_registros }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <p class="text-center text-muted">No hay datos para estos filtros.</p>
      @endif
    </div>

    @if(request()->filled('nombre_producto')||request()->filled('categoria_id'))
      <a href="{{ route('movimientos.statsPdf', request()->only('nombre_producto','categoria_id')) }}"
         class="btn btn-secondary mb-4">
        📄 Descargar estadísticas (PDF)
      </a>
    @endif
  <h2>Histórico de Movimientos</h2>
    <div class="controls mb-3">
      <input type="text" id="searchInput" class="form-control" placeholder="🔍 Buscar…">
      <select id="typeFilter" class="form-control">
        <option value="">Todos los tipos</option>
        <option value="Entrada">Entrada</option>
        <option value="Salida">Salida</option>
      </select>
      <input type="date" id="dateFilter" class="form-control">
      <button id="toggleAll" class="btn btn-outline-primary">↕ Mostrar todo</button>

<a 
  id="btnPdf"
  href="{{ route('movimientos.pdf') }}"
  data-url="{{ route('movimientos.pdf') }}"
  class="btn-pdf"
>
  📄 Descargar PDF
</a>
    </div>

    <div class="table-wrap collapsed">
      <table>
        <thead>
          <tr>
            <th>#</th><th>Fecha</th><th>Producto</th><th>Tipo</th><th>Cantidad</th><th>Descripción</th>
          </tr>
        </thead>
        <tbody>
          @forelse($movimientos as $mov)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ $mov->fecha }}</td>
              <td>{{ $mov->producto->nombre }}</td>
              <td>{{ ucfirst($mov->tipo) }}</td>
              <td>{{ $mov->cantidad }}</td>
              <td>{{ $mov->descripcion ?? '—' }}</td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center">No hay movimientos registrados.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

   <script src="{{ asset('js/Bodega/MovYestadisticas.js') }}"></script>
</body>
</html>
