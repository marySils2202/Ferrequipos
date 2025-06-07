<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Créditos – Carrito y Cuentas Pendientes</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/credito.css') }}">
</head>
<body>
  <a href="{{ route('factura') }}" class="back-corner" title="Volver">←</a>
  <a href="{{ route('clientes.index') }}" class="btn-float" title="Gestión de Clientes">👤</a>

  <div class="wrapper">
      <div class="header-container">
    <img src="{{ asset('imagenes/logo_quinteros.png') }}"
         alt="Logo Moto Repuestos Quinteros"
         class="logo">
  </div>
    <div id="globalAlert">
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      @if(session('print_invoice_id'))
        <a href="{{ route('facturacion.print', session('print_invoice_id')) }}"
           target="_blank"
           class="btn btn-primary mb-3">
          🖨️ Imprimir factura #{{ session('print_invoice_id') }}
        </a>
      @endif
    </div>

    <h2>Nuevo Crédito</h2>
    <form id="carritoForm" action="{{ route('creditos.store') }}" method="POST">
      @csrf
      <input type="hidden" name="id_cliente" id="clienteHidden">

      <div class="row g-3 mb-3">
        <div class="col-md-5">
          <label class="form-label">Cliente</label>
          <input list="clientesList"
                 id="clienteInput"
                 class="form-control"
                 placeholder="Escribe para buscar…"
                 autocomplete="off">
          <datalist id="clientesList">
            @foreach($clientes as $cli)
              <option data-id="{{ $cli->id_cliente }}"
                      value="{{ $cli->nombre }}"></option>
            @endforeach
          </datalist>
        </div>

        <div class="col-md-2 d-flex align-items-end">
          <button type="button"
                  class="btn btn-secondary w-100 btn-registrar-cliente"
                  data-bs-toggle="modal"
                  data-bs-target="#modalNuevoCliente">
            <span class="icon">➕</span>
            <span>Registrar Cliente</span>
          </button>
        </div>
      </div>
      <div class="row g-3 mb-4">
        <div class="col-md-5">
          <label class="form-label">Producto</label>
          <input list="inventariosList"
                 id="productoInput"
                 class="form-control"
                 placeholder="Escribe para buscar…"
                 autocomplete="off">
          <datalist id="inventariosList">
            @foreach($inventarios as $inv)
              <option
                data-id="{{ $inv->producto->id_producto }}"
                data-nombre="{{ $inv->producto->nombre }}"
                data-precio="{{ $inv->precio ?? $inv->producto->precio_venta }}"
                data-stock="{{ $inv->cantidad_stock }}"
                data-descripcion="{{ $inv->producto->descripcion }}"
                value="{{ $inv->producto->nombre }}">
              </option>
            @endforeach
          </datalist>
        </div>
        <div class="col-md-5">
          <label class="form-label">Detalle del Producto</label>
          <textarea id="descripcionProducto"
                    class="form-control"
                    rows="2"
                    readonly
                    placeholder="Aquí aparecerá la descripción…"></textarea>
        </div>

        <div class="col-md-1">
          <label class="form-label">Cant.</label>
          <input id="cantidadInput"
                 type="number"
                 class="form-control"
                 min="1"
                 value="1">
        </div>

        <div class="col-md-1 d-flex align-items-end">
          <button id="addBtn"
                  type="button"
                  class="btn btn-secondary w-100">
            Añadir
          </button>
        </div>
      </div>
      <table id="carritoTabla"
             class="table table-sm table-bordered d-none">
        <thead class="table-light">
          <tr>
            <th>Producto</th>
            <th>Detalle</th>
            <th>Cant.</th>
            <th>Precio u.</th>
            <th>Subtotal</th>
            <th></th>
          </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
          <tr>
            <th colspan="4" class="text-end">Total:</th>
            <th id="totalCell">0.00</th>
            <th></th>
          </tr>
        </tfoot>
      </table>
      <button id="realizarBtn"
              type="submit"
              class="btn btn-warning d-none mt-3">
        Realizar Crédito
      </button>
    </form>

    <hr class="my-5">
    <h2>Cuentas Pendientes</h2>
    @if($creditosPendientes->isEmpty())
      <p class="text-center">No hay cuentas pendientes.</p>
    @else
      <table class="table table-bordered">
        <thead class="table-light">
          <tr>
            <th>Cliente</th>
            <th>Saldo</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach($creditosPendientes as $d)
            <tr>
              <td>{{ $d['cliente']->nombre }}</td>
              <td>C$ {{ number_format($d['saldo'],2) }}</td>
              <td class="d-flex gap-2 justify-content-center">
                <button class="btn btn-success btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#abonoModal"
                        data-idc="{{ $d['id_credito'] }}"
                        data-saldo="{{ $d['saldo'] }}">
                  Abonar
                </button>

                <form id="formReembolso-{{ $d['id_credito'] }}"
                      action="{{ route('creditos.reembolso', $d['id_credito']) }}"
                      method="POST">
                  @csrf
                  @method('DELETE')
                  <button type="button"
                          class="btn btn-danger btn-sm"
                          data-credito="{{ $d['id_credito'] }}"
                          onclick="reembolsar(this)">
                    Reembolso
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif

  </div>
  <button type="button"
          class="btn btn-secondary btn-limpiar"
          onclick="window.location.reload()">
    🧹
    <span>Limpiar</span>
  </button>
  <div class="modal fade" id="abonoModal" tabindex="-1">
    <div class="modal-dialog">
      <form id="abonoForm" method="POST">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Registrar Abono</h5>
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
            </button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Método de pago</label>
              <select name="metodo" class="form-select">
                <option value="efectivo">Efectivo</option>
                <option value="tarjeta">Tarjeta</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Monto</label>
              <input type="number"
                     step="0.01"
                     name="monto_abono"
                     id="montoInput"
                     class="form-control"
                     required>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary">Guardar Abono</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="modal fade" id="modalNuevoCliente" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Nuevo Cliente</h5>
          <button type="button" class="btn-close"
                  data-bs-dismiss="modal"></button>
        </div>

        <form id="formNuevoCliente" method="POST"
              action="{{ route('clientes.store') }}">
          @csrf
          <div class="modal-body">
            <div class="mb-2">
              <input name="nombre"
                     type="text"
                     class="form-control"
                     placeholder="Nombre*"
                     required>
            </div>
            <div class="mb-2">
              <input name="direccion"
                     type="text"
                     class="form-control"
                     placeholder="Dirección">
            </div>
            <div class="mb-2">
              <input name="telefono"
                     type="text"
                     class="form-control"
                     placeholder="Teléfono">
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit"
                    class="btn btn-success w-100">
              ➕ Agregar Cliente
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

   <script src="{{ asset('js/Facturacion/Credito.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
