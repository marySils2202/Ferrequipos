<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>📘 Libro de Ventas Diario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/LibroDeVentas.css') }}">
</head>
<body>
  <a href="{{ route('sistema') }}" class="back-corner" title="Volver">←</a>
  <div class="movements-card">
    <h2 class="page-title">📘 Libro de Ventas Diario</h2>

    <div class="filter-controls">
      <input type="date" id="fechaFiltro" name="fechaFiltro" value="{{ $fechaFiltro ?? '' }}"
             class="form-control form-control-sm">
      <button id="btnFiltrar" class="btn btn-sm">Filtrar</button>
      <button id="toggleTabla" class="btn btn-sm">↕ Mostrar todo</button>
      <a href="{{ route('estadisticas.index') }}"
         class="btn btn-sm btn-outline-secondary"
         style="margin-left: 0.5rem;">
        📊 Estadísticas 
      </a>
    </div>

    <div class="table-responsive table-wrapper collapsed" id="tablaWrap">
      <table class="table table-striped text-center" id="tablaLibro">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Venta del Día</th>
            <th>Ganancia Diaria</th>
            <th>Cerró Caja</th>
          </tr>
        </thead>
        <tbody>
          @forelse($ventas as $venta)
            <tr>

              <td data-fecha="{{ $venta->fecha->format('Y-m-d') }}">
                {{ $venta->fecha->locale('es')->translatedFormat('j \d\e F Y') }}
              </td>
              <td>₡S {{ number_format($venta->arqueo->diferencia, 2) }}</td>
              <td>₡S {{ number_format($venta->ganancia_diaria, 2) }}</td>
              <td>{{ optional($venta->arqueo->impresor)->nombre ?? '—' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center">No hay registros para mostrar.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <script src="{{ asset('js/Contabilidad/LibrodeVentas.js') }}"></script>
</body>
</html>
