<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Pago a Mecánico – Semanal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f4f4f4; color: #333; font-family: 'Segoe UI',sans-serif; padding: 2rem; }
    .back-corner {
      position: fixed; top: 20px; left: 20px;
      width: 48px; height: 48px; border-radius: 50%;
      background: linear-gradient(135deg,#6d5bff,#a986ff);
      color: #fff; font-size: 1.5rem; line-height: 48px;
      text-align: center; text-decoration: none;
      box-shadow: 0 4px 12px rgba(0,0,0,.15);
      transition: transform .2s, opacity .2s; z-index: 1000;
    }
    .back-corner:hover { transform: translateY(-2px); opacity: .9; }
    .wrapper {
      background: #fff; padding: 30px; border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,.1);
      max-width: 1080px; margin: auto;
    }
    .card { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .card-header {
      background: #4f46e5; color: #fff; padding: 1rem;
      border-top-left-radius:8px; border-top-right-radius:8px;
    }
    .card-body { padding: 1.5rem; }
    table { margin-bottom: 0; }
    th, td { vertical-align: middle!important; }
  </style>
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
  <div class="modal fade" id="modalAgregarMecanico" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form method="POST" action="{{ route('mecanicos.store') }}">
        @csrf
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title">Agregar Mecánico</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <label>Nombre:</label>
            <input type="text" name="nombre" class="form-control" required>
          </div>
          <div class="modal-footer">
            <button class="btn btn-success w-100">Guardar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="modal fade" id="modalEliminarMecanico" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">Eliminar Mecánico</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Selecciona un mecánico para eliminar:</p>
          <ul class="list-group">
            @foreach($mechanics as $me)
              <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ $me->nombre }}
                <form method="POST" action="{{ route('mecanicos.destroy', $me->id_mecanico) }}" onsubmit="return confirm('¿Eliminar {{ $me->nombre }}?')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-danger">Eliminar</button>
                </form>
              </li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
