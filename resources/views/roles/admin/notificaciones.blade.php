<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Notificaciones</title>
  <link rel="stylesheet" href="{{ asset('css/Notificaciones.css') }}">
</head>
<body>
  @php use App\Models\Inventario; @endphp

  <div class="card-container">
    <h2>🔔 Notificaciones</h2>

    <div class="accordion-item">
      <button class="accordion-header" onclick="toggleAccordion(this,'bodyLastPurchase')">
        🛒 Última Compra <span class="accordion-icon">▶</span>
      </button>
      <div id="bodyLastPurchase" class="accordion-body">
        @if($ultima)
          <div class="alert alert-success">
            Última compra registrada: <strong>{{ $ultima }}</strong>
          </div>
        @else
          <div class="alert alert-warning">
            Aún no se ha registrado ninguna compra.
          </div>
        @endif
      </div>
    </div>

    <div class="accordion-item">
      <button class="accordion-header" onclick="toggleAccordion(this,'bodyLastSupplier')">
        🏷️ Último Proveedor <span class="accordion-icon">▶</span>
      </button>
      <div id="bodyLastSupplier" class="accordion-body">
        @if($ultimoProveedor)
          <div class="alert alert-success">
            Último proveedor agregado: <strong>{{ $ultimoProveedor->nombre }}</strong>
          </div>
        @else
          <div class="alert alert-warning">
            No hay proveedores agregados.
          </div>
        @endif
      </div>
    </div>

    <div class="accordion-item">
      <button class="accordion-header" onclick="toggleAccordion(this,'bodyLastProduct')">
        📦 Último Producto <span class="accordion-icon">▶</span>
      </button>
      <div id="bodyLastProduct" class="accordion-body">
        @if($ultimoProducto)
          <div class="alert alert-success">
            Último producto agregado: <strong>{{ $ultimoProducto->nombre }}</strong>
          </div>
        @else
          <div class="alert alert-warning">
            No hay productos agregados.
          </div>
        @endif
      </div>
    </div>


    <div class="accordion-item">
      <button class="accordion-header" onclick="toggleAccordion(this,'bodyLastClient')">
        👤 Último Cliente <span class="accordion-icon">▶</span>
      </button>
      <div id="bodyLastClient" class="accordion-body">
        @if($ultimoCliente)
          <div class="alert alert-success">
            <p><strong>Nombre:</strong> {{ $ultimoCliente->nombre }}</p>
            <p><strong>Dirección:</strong> {{ $ultimoCliente->direccion }}</p>
            <p><strong>Teléfono:</strong> {{ $ultimoCliente->telefono }}</p>
          </div>
        @else
          <div class="alert alert-warning">
            No hay clientes agregados.
          </div>
        @endif
      </div>
    </div>

    <div class="accordion-item">
      <button class="accordion-header" onclick="toggleAccordion(this,'bodyLastSale')">
        🧾 Última Venta <span class="accordion-icon">▶</span>
      </button>
      <div id="bodyLastSale" class="accordion-body">
        @if($ultimaFactura)
          <div class="alert alert-success">
            <p><strong>Fecha:</strong> {{ $ultimaFactura->fecha->format('d/m/Y H:i:s') }}</p>
            <p><strong>Facturó:</strong> {{ $ultimaFactura->usuario->nombre }}</p>
            <p><strong>Cliente:</strong> {{ $ultimaFactura->cliente->nombre }}</p>
          </div>
        @else
          <div class="alert alert-warning">
            No hay ventas registradas.
          </div>
        @endif
      </div>
    </div>

    <div class="accordion-item">
      <button class="accordion-header" onclick="toggleAccordion(this,'bodyLastCredit')">
        💳 Último Crédito <span class="accordion-icon">▶</span>
      </button>
      <div id="bodyLastCredit" class="accordion-body">
        @if($ultimoCredito)
          <div class="alert alert-success">
            <p><strong>Crédito #</strong> {{ $ultimoCredito->id_credito }}</p>
            <p><strong>Cliente:</strong> {{ $ultimoCredito->factura->cliente->nombre }}</p>
            <p><strong>Monto Total:</strong> C$ {{ number_format($ultimoCredito->monto_total, 2) }}</p>
            <p><strong>Saldo Pendiente:</strong> C$ {{ number_format($ultimoCredito->factura->saldo_pendiente, 2) }}</p>
{{ optional($ultimoCredito->created_at)->format('d/m/Y H:i:s') ?? '' }}

          </div>
        @else
          <div class="alert alert-warning">
            No hay créditos registrados.
          </div>
        @endif
      </div>
    </div>

    <div class="accordion-item">
      <button class="accordion-header" onclick="toggleAccordion(this,'bodyAlerts')">
        📉 Alertas de Stock <span class="accordion-icon">▶</span>
      </button>
      <div id="bodyAlerts" class="accordion-body">
        @if($alertas->isEmpty())
          <div class="alert alert-success">
            Todos los productos están por encima del stock mínimo.
          </div>
        @else
          <table class="table">
            <thead>
              <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Stock Actual</th>
                <th>Stock Mínimo</th>
              </tr>
            </thead>
            <tbody>
              @foreach($alertas as $item)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $item->producto->nombre }}</td>
                  <td>{{ $item->cantidad_stock }}</td>
                  <td>{{ $item->producto->stock_minimo }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @endif
      </div>
    </div>

    <a href="{{ route('sistema') }}" class="botones-finale">Regresar a Inicio</a>
  </div>
 <script src="{{ asset('js/Mantenimiento/Notificaciones.js') }}"></script>
</body>
</html>
