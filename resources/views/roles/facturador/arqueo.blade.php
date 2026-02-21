<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Arqueo de Caja</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/arqueo.css') }}">
</head>
<body>
  <a href="{{ route('sistema') }}" class="back-corner" title="Volver">←</a>
  <div class="card-main container py-4">
    <h2 class="page-title mb-4">🔐 Arqueo de Caja</h2>

    <div class="d-flex justify-content-end mb-4 gap-2">
      <a href="{{ route('arqueo.pdf') }}" class="btn btn-outline-secondary">
        🖨️ Descargar reporte de ventas (PDF)
      </a>
      <a href="{{ route('facturacion.pago_mecanico') }}" class="btn btn-outline-secondary">
        Pagos Semanales
      </a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @php
      $editing      = isset($editArqueo);
      $ultimoCierre = $ultimoCierre ?? 0;
    @endphp

    @if($editing)
      <div class="ark-card mb-4">
        <h5 class="mb-3">✏️ Editar Arqueo #{{ $editArqueo->id_arqueo }}</h5>
        <form method="POST" action="{{ route('arqueo.update', $editArqueo->id_arqueo) }}">
          @csrf @method('PUT')
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Monto final</label>
              <input name="monto_final" type="number" step="0.01"
                     class="form-control @error('monto_final') is-invalid @enderror"
                     value="{{ old('monto_final', $editArqueo->monto_final) }}" required>
              @if($errors->has('monto_final'))
                <div class="invalid-feedback">{{ $errors->first('monto_final') }}</div>
              @endif
            </div>
            <div class="col-md-3">
              <label class="form-label">Salida de caja</label>
              <input name="salida_caja" type="number" step="0.01"
                     class="form-control @error('salida_caja') is-invalid @enderror"
                     value="{{ old('salida_caja', $editArqueo->salida_caja) }}">
              @if($errors->has('salida_caja'))
                <div class="invalid-feedback">{{ $errors->first('salida_caja') }}</div>
              @endif
            </div>
            <div class="col-md-4">
              <label class="form-label">Razón salida</label>
              <input name="razon_salida" type="text"
                     class="form-control @error('razon_salida') is-invalid @enderror"
                     value="{{ old('razon_salida', $editArqueo->razon_salida) }}">
              @if($errors->has('razon_salida'))
                <div class="invalid-feedback">{{ $errors->first('razon_salida') }}</div>
              @endif
            </div>
            <div class="col-auto d-flex align-items-end gap-2">
              <button class="btn btn-warning">Guardar cambios</button>
              <a href="{{ route('arqueo.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
          </div>
        </form>
      </div>

    @elseif(!$abierto)
      <div class="ark-card mb-4">
        <h5 class="mb-3">Iniciar nuevo arqueo</h5>
        <form method="POST" action="{{ route('arqueo.store') }}">
          @csrf
          <div class="row g-3 align-items-end">
            <div class="col-md-4">
              <label class="form-label">Monto inicial</label>
              <input name="monto_inicial" type="number" step="0.01"
                     class="form-control @error('monto_inicial') is-invalid @enderror"
                     value="{{ old('monto_inicial', $ultimoCierre) }}" required>
              @if($errors->has('monto_inicial'))
                <div class="invalid-feedback">{{ $errors->first('monto_inicial') }}</div>
              @endif
            </div>
            <div class="col-auto">
              <button class="btn btn-primary">🚀 Iniciar</button>
            </div>
          </div>
        </form>
      </div>

    @else
      <div class="ark-card mb-4">
        <h5 class="mb-3">Cerrar Arqueo #{{ $abierto->id_arqueo }}</h5>
        <form method="POST" action="{{ route('arqueo.cerrar', $abierto->id_arqueo) }}">
          @csrf
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Monto final</label>
              <input type="text" class="form-control" disabled
                     value="₡S {{ number_format($montoFinalCalculado, 2) }}">
              <input type="hidden" name="monto_final" value="{{ $montoFinalCalculado }}">
            </div>
            <div class="col-md-3">
              <label class="form-label">Salida de caja</label>
              <input name="salida_caja" type="number" step="0.01"
                     class="form-control @error('salida_caja') is-invalid @enderror"
                     value="{{ old('salida_caja') }}">
              @if($errors->has('salida_caja'))
                <div class="invalid-feedback">{{ $errors->first('salida_caja') }}</div>
              @endif
            </div>
            <div class="col-md-4">
              <label class="form-label">Razón salida</label>
              <input name="razon_salida" type="text"
                     class="form-control @error('razon_salida') is-invalid @enderror"
                     value="{{ old('razon_salida') }}">
              @if($errors->has('razon_salida'))
                <div class="invalid-feedback">{{ $errors->first('razon_salida') }}</div>
              @endif
            </div>
            <div class="col-auto d-flex align-items-end">
              <button class="btn btn-success">✅ Cerrar</button>
            </div>
          </div>
        </form>
      </div>
    @endif

    <div class="filter-controls mb-3 d-flex gap-2">
      <input type="date" id="fechaFiltro" class="form-control form-control-sm">
      <button id="btnFiltrar" class="btn btn-sm btn-primary">Filtrar</button>
      <button id="toggleTabla" class="btn btn-sm btn-outline-secondary">↕ Mostrar todo</button>
    </div>

    <div class="ark-card">
      <h5 class="mb-3">Historial de arqueos</h5>
      <div class="table-responsive table-wrapper collapsed" id="historialWrap">
        <table class="table table-striped text-center align-middle" id="tablaArqueos">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>Inicial</th>
              <th>Final</th>
              <th>Salida</th>
              <th>Razón</th>
              <th>Diferencia</th>
              <th>Fecha</th>
              <th>Cerró</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($arqueos as $a)
              <tr @class(['table-warning' => is_null($a->monto_final)])>
                <td>{{ $loop->iteration }}</td>
                <td>₡S {{ number_format($a->monto_inicial, 2) }}</td>
                <td>{{ $a->monto_final !== null
                     ? '₡S '.number_format($a->monto_final, 2)
                     : '—' }}</td>
                <td>₡S {{ number_format($a->salida_caja, 2) }}</td>
                <td>{{ $a->razon_salida ?? '—' }}</td>
                <td>₡S {{ number_format($a->diferencia, 2) }}</td>
                <td data-fecha="{{ substr($a->fecha, 0, 10) }}">
                  {{ \Carbon\Carbon::parse($a->fecha)->format('Y-m-d H:i') }}
                </td>
                <td>{{ optional($a->impresor)->nombre ?? '—' }}</td>
                <td class="d-flex justify-content-center gap-1">
                  @if(!is_null($a->monto_final))
                    @auth
                      @if(auth()->user()->rol === 'admin')
                        <a href="{{ route('arqueo.edit', $a->id_arqueo) }}" class="btn btn-sm btn-outline_warning" title="Editar">✏️</a>
                      @endif
                    @endauth
                    <a href="{{ route('arqueo.print', $a->id_arqueo) }}" class="btn btn-sm btn-outline-primary" target="_blank" title="Imprimir">🖨️</a>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center">No hay registros.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
   <script src="{{ asset('js/Contabilidad/Arqueo.js') }}"></script>
</body>
</html>