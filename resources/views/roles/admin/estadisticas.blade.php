<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>📊 Estadísticas de Ventas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/Estadisticas.css') }}">
</head>
<body>

  <a href="{{ route('sistema') }}" class="back-corner" title="Volver">←</a>

  <div class="container my-4">
    <h2 class="page-title text-center mb-4">📈 Estadísticas Dinámicas</h2>
    <form method="GET" action="{{ route('estadisticas.index') }}" class="row g-2 mb-4">
      <div class="col-auto">
        <select name="tipo" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="month" {{ $tipo === 'month' ? 'selected' : '' }}>Por Mes</option>
          <option value="week"  {{ $tipo === 'week'  ? 'selected' : '' }}>Por Semana</option>
        </select>
      </div>

      <div class="col-auto">
        <select name="mes" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="">— Elige Mes —</option>
          @foreach($opMes as $key => $label)
            <option value="{{ $key }}" {{ ($mes ?? '') === $key ? 'selected' : '' }}>
              {{ $label }}
            </option>
          @endforeach
        </select>
      </div>

      @if($tipo === 'week' && $mes)
        <div class="col-auto">
          <select name="semana" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">— Elige Semana —</option>
            @foreach($opSem as $key => $label)
              <option value="{{ $key }}" {{ ($semana ?? '') === $key ? 'selected' : '' }}>
                {{ $label }}
              </option>
            @endforeach
          </select>
        </div>
      @endif

      <div class="col-auto">
        <button type="submit" class="btn btn-sm btn-primary">Cargar</button>
      </div>
      <div class="col-auto">
        <a href="{{ route('estadisticas.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
      </div>
    </form>

    @if( ($tipo === 'month' && $mes) || ($tipo === 'week' && $semana) )
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4 mb-4">
        <div class="col">
          <div class="card text-white bg-info h-100">
            <div class="card-body">
              <h6 class="card-title">Venta Total</h6>
              <p class="card-text fs-4">₡S {{ number_format($totalVentasPeriodo, 2) }}</p>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card text-white bg-success h-100">
            <div class="card-body">
              <h6 class="card-title">Ganancia Total</h6>
              <p class="card-text fs-4">₡S {{ number_format($totalGananciasPeriodo, 2) }}</p>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card text-white bg-warning h-100">
            <div class="card-body">
              <h6 class="card-title">Unidades Vendidas</h6>
              <p class="card-text fs-4">{{ $totalUnidadesVendidas }}</p>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card text-white bg-dark h-100">
            <div class="card-body">
              <h6 class="card-title">Mano de Obra Total</h6>
              <p class="card-text fs-4">₡S {{ number_format($manoObraTotal, 2) }}</p>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card text-white bg-secondary h-100">
            <div class="card-body">
              <h6 class="card-title">Descuento Total</h6>
              <p class="card-text fs-4">₡S {{ number_format($descuentoTotal, 2) }}</p>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card text-white bg-secondary h-100">
            <div class="card-body">
              <h6 class="card-title">Promedio Desc. por Factura</h6>
              <p class="card-text fs-4">₡S {{ number_format($promedioDescuento, 2) }}</p>
              <small class="text-light">{{ $facturasConDescuento }} facturas con descuento</small>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card text-white bg-primary h-100">
            <div class="card-body">
              <h6 class="card-title">Cliente con Más Visitas</h6>
              @if($clienteMasVisitas)
                <p class="card-text">
                  {{ $clienteMasVisitas }}<br>
                  <small>{{ $visitasClienteMax }} facturas</small>
                </p>
              @else
                <p class="card-text">—</p>
              @endif
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card text-white bg-danger h-100">
            <div class="card-body">
              <h6 class="card-title">Día más Productivo</h6>
              @if($diaMasProductivo)
                <p class="card-text">
                  {{ $diaMasProductivo }}<br>
                  <small>₡S {{ number_format($ventasDiaMasProductivo, 2) }}</small>
                </p>
              @else
                <p class="card-text">—</p>
              @endif
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card text-white bg-info h-100">
            <div class="card-body">
              <h6 class="card-title">Mecánico con Más Mano de Obra</h6>
              @if($mecanicoMasMano)
                <p class="card-text">
                  {{ $mecanicoMasMano }}<br>
                  <small>₡S {{ number_format($manoObraMecanicoMax, 2) }}</small>
                </p>
              @else
                <p class="card-text">—</p>
              @endif
            </div>
          </div>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col-lg-8 mb-4">
          <div class="card h-100">
            <div class="card-header text-center">
              {{ $tipo === 'week' ? 'Gráfico Semanal' : 'Gráfico Mensual' }}
            </div>
            <div class="card-body" style="height: 400px;">
              {!! $chartVentasYGanancias->container() !!}
            </div>
          </div>
        </div>
        <div class="col-lg-4 mb-4">
          <div class="card h-100">
            <div class="card-header text-center">
              Productos Vendidos por Categoría
            </div>
            <div class="card-body" style="height: 400px;">
              {!! $chartCategorias->container() !!}
            </div>
          </div>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col-lg-6 mb-4">
          <div class="card h-100">
            <div class="card-header text-center">
              Facturas por Cliente
            </div>
            <div class="card-body" style="height: 350px;">
              {!! $chartClientes->container() !!}
            </div>
          </div>
        </div>
        <div class="col-lg-6 mb-4">
          <div class="card h-100">
            <div class="card-header text-center">
              Mano de Obra por Mecánico
            </div>
            <div class="card-body" style="height: 350px;">
              {!! $chartMecanicos->container() !!}
            </div>
          </div>
        </div>
      </div>

    @else

      <div class="alert alert-info text-center">
        Selecciona {{ $tipo === 'week' ? 'un mes y una semana' : 'un mes' }} para ver las estadísticas.
      </div>
    @endif
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  {!! $chartVentasYGanancias->script() !!}
  {!! $chartCategorias->script() !!}
  {!! $chartClientes->script() !!}
  {!! $chartMecanicos->script() !!}
</body>
</html>
