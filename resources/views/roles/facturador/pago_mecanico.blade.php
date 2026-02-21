<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Pago a Mecánico – Semanal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/Facturacion/PagoMecanico.css') }}">
</head>
<body>

  <a href="{{ route('sistema') }}" class="back-corner" title="Volver a Inicio">←</a>

  <div class="wrapper">
    <div class="card">
      <div class="card-header">
        <h2 class="mb-0">💰 Pago a Mecánico (Semanal)</h2>
      </div>
      <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
          <div class="col-md-4">
            <label for="mecanico_id" class="form-label">Mecánico (opcional)</label>
            <div class="input-group">
              <select name="mecanico_id" id="mecanico_id" class="form-select">
                <option value="">— Todos —</option>
                @foreach($mechanics as $me)
                  <option value="{{ $me->id_mecanico }}"
                    {{ request('mecanico_id') == $me->id_mecanico ? 'selected' : '' }}>
                    {{ $me->nombre }}
                  </option>
                @endforeach
              </select>
              <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalAgregarMecanico" title="Agregar mecánico">➕</button>
              <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalEliminarMecanico" title="Eliminar mecánico">🗑️</button>
            </div>
          </div>
          <div class="col-md-4">
            <label for="start" class="form-label">Inicio de Semana</label>
            <input type="date" name="start" id="start" class="form-control"
              value="{{ request('start', $start->toDateString()) }}">
          </div>
          <div class="col-md-4">
            <label for="end" class="form-label">Fin de Semana</label>
            <input type="date" name="end" id="end" class="form-control"
              value="{{ request('end', $end->toDateString()) }}">
          </div>
          <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary">Filtrar</button>
          </div>
        </form>
        <div class="subtitle mb-3">
          <p><strong>Semana:</strong> {{ $start->format('Y-m-d') }} – {{ $end->format('Y-m-d') }}</p>
        </div>

        @if($weeklyData->isEmpty())
          <p class="text-center text-muted">
            No hay datos de mano de obra para el rango y mecánico seleccionado.
          </p>
        @else
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Mecánico</th>
                <th class="text-end">Mano de Obra</th>
                <th class="text-end">70% Mecánico</th>
                <th class="text-end">30% Empresa</th>
              </tr>
            </thead>
            <tbody>
              @foreach($weeklyData as $row)
                <tr>
                  <td>{{ \Carbon\Carbon::parse($row['date'])->format('Y-m-d') }}</td>
                  <td>{{ $row['mecanico'] }}</td>
                  <td class="text-end">C${{ number_format($row['mano_obra'], 2) }}</td>
                  <td class="text-end">C${{ number_format($row['pago'],      2) }}</td>
                  <td class="text-end">C${{ number_format($row['empresa'],   2) }}</td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th colspan="2">Total:</th>
                <th class="text-end">C${{ number_format($weeklyData->sum('mano_obra'),2) }}</th>
                <th class="text-end">C${{ number_format($weeklyData->sum('pago'),     2) }}</th>
                <th class="text-end">C${{ number_format($weeklyData->sum('empresa'),  2) }}</th>
              </tr>
            </tfoot>
          </table>
          <div class="mt-4 text-end">
            <a href="{{ route('facturacion.recibo.pdf', [
                  'start'       => request('start'),
                  'end'         => request('end'),
                  'mecanico_id' => request('mecanico_id'),
                ]) }}"
               class="btn btn-success">
              🖨️ Descargar Nomina PDF
            </a>
          </div>
        @endif

      </div>
    </div>
  </div>
    @include('roles.facturador.pago_mecanico.partials.agregar_mecanico_modal')
  @include('roles.facturador.pago_mecanico.partials.eliminar_mecanico_modal')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
