<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Facturación – Moto Repuestos Quinteros</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/Factura.css') }}">
  <link rel="stylesheet" href="{{ asset('css/Factura2.css') }}">
  <link
    href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.default.min.css"
    rel="stylesheet">
</head>
<body>
  <div class="container py-4">

    <div class="invoice-header d-flex align-items-center justify-content-between mb-4">
      <h1 class="m-0">Moto Repuestos Quinteros</h1>
      <img
        src="{{ asset('imagenes/logo_quinteros.png') }}"
        alt="Logo Moto Repuestos Quinteros"
        class="logo"
        style="max-height: 80px;">
    </div>
    <div id="globalAlert">
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      @if(session('success') && session('invoice_id'))
        <a
          id="printBtn"
          href="{{ route('facturacion.print', session('invoice_id')) }}"
          target="_blank"
          class="btn btn-primary mb-3">
          🖨️ Imprimir Factura
        </a>
      @endif

      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
    </div>

    <form
      method="POST"
      id="facturaForm"
      action="{{ route('facturacion.store') }}">
      @csrf

      <div class="row-flex mb-4">
        <div class="col-half">
          <h4>Datos de la Factura</h4>
          <label>Facturador:</label>
          <input
            type="text"
            class="form-control-plaintext"
            readonly
            value="{{ auth()->user()->nombre }}">
        </div>

        <div class="col-half">
          <h4>Datos del Cliente</h4>
          <datalist id="clientesList">
            @foreach($clientes as $c)
              <option data-id="{{ $c->id_cliente }}" value="{{ $c->nombre }}"></option>
            @endforeach
          </datalist>
          <label>Cliente:</label>
          <input
            list="clientesList"
            class="form-control cliente-input"
            placeholder="Nombre de Cliente…"
            required>
          <input
            type="hidden"
            name="id_cliente"
            class="cliente-id">

          <div class="mt-2">
            <button
              type="button"
              class="btn btn-secondary w-100"
              data-bs-toggle="modal"
              data-bs-target="#modalNuevoCliente">
              ➕ Registrar Cliente
            </button>
          </div>
        </div>
      </div>

      <h4>Detalle de Factura</h4>
      <datalist id="productosList">
        @foreach($inventarios as $inv)
          <option
            data-id="{{ $inv->producto->id_producto }}"
            data-precio="{{ $inv->precio ?? $inv->producto->precio_venta }}"
            data-stock="{{ $inv->cantidad_stock }}"
            data-descripcion="{{ $inv->producto->descripcion }}"
            value="{{ $inv->producto->nombre }}">
          </option>
        @endforeach
      </datalist>

      <div class="mb-3 text-end">
        <button
          type="button"
          class="btn btn-success"
          data-bs-toggle="modal"
          data-bs-target="#modalRegistrarCompra">
          📦 Compra de emergencia
        </button>
      </div>

      <table class="table">
        <thead>
          <tr>
            <th>Producto</th>
            <th>Descripción</th>
            <th>Cant.</th>
            <th>Precio U.</th>
            <th>Subtotal</th>
            <th class="actions">Acciones</th>
          </tr>
        </thead>
        <tbody id="lineas">
          <tr>
            <td>
              <input
                list="productosList"
                class="form-control prod-input"
                placeholder="Producto"
                required>
              <input
                type="hidden"
                name="id_producto[]"
                class="prod-id">
            </td>
            <td>
              <input
                name="descripcion[]"
                type="text"
                class="form-control desc"
                readonly>
            </td>
            <td>
              <input
                name="cantidad[]"
                type="number"
                min="1"
                value="1"
                class="form-control cant"
                required>
            </td>
            <td>
              <input
                name="precio_unitario[]"
                type="text"
                class="form-control prec"
                readonly>
            </td>
            <td>
              <input
                name="subtotal[]"
                type="text"
                class="form-control sub"
                readonly>
            </td>
            <td class="actions">
              <button
                type="button"
                class="btn btn-success add"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="Agregar una nueva fila">
                ➕
              </button>
              <button
                type="button"
                class="btn btn-warning del"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="Eliminar esta fila">
                🗑️
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <div class="row-flex mb-4">
        <div class="col-half">
          <div class="total-section">
            <h4>Totales</h4>
            <label>Subtotal productos:</label>
            <input
              id="subTot"
              type="text"
              class="form-control-plaintext"
              readonly
              value="0.00">

            <label>Descuento (Opcional):</label>
            <input
              name="descuento"
              id="descuento"
              type="number"
              step="0.01"
              min="0"
              class="form-control mb-2"
              value="{{ old('descuento', 0) }}">

            <label>Total (prod + MO - Desc):</label>
            <input
              id="tot"
              type="text"
              name="total"
              class="form-control-plaintext"
              readonly
              value="0.00">
          </div>
        </div>

        <div class="col-half">
          <div class="total-section">
            <h4>Pago</h4>
            <label>Método de pago:</label>
            <select
              name="metodo_pago"
              id="metodoPago"
              class="form-select mb-2">
              <option value="efectivo">Efectivo</option>
              <option value="tarjeta">Tarjeta</option>
            </select>

            <div class="mb-4">
              <label for="mecanico_id">Mecánico (opcional):</label>
              <select
                name="mecanico_id"
                id="mecanico_id"
                class="form-select">
                <option value="">— Ninguno —</option>
                @foreach($mechanics as $me)
                  <option
                    value="{{ $me->id_mecanico }}"
                    {{ old('mecanico_id') == $me->id_mecanico ? 'selected' : '' }}>
                    {{ $me->nombre }}
                  </option>
                @endforeach
              </select>
              @error('mecanico_id')
                <div class="text-danger">{{ $message }}</div>
              @enderror
            </div>

            <label>Mano de obra (Opcional):</label>
            <input
              name="mano_obra_general"
              id="moGen"
              type="number"
              step="0.01"
              class="form-control mb-2"
              value="{{ old('mano_obra_general', 0) }}"
              required>

            <label>Monto pago:</label>
            <input
              id="pagoVis"
              type="number"
              class="form-control mb-2"
              required>

            <label>Vuelto:</label>
            <input
              id="vuelto"
              type="text"
              name="vuelto"
              class="form-control"
              readonly
              value="0.00">
          </div>
        </div>
      </div>

      <input
        type="hidden"
        name="monto_pago"
        id="pago">

      <div class="botones-finales mb-5">
        <button
          id="facturarFab"
          type="submit"
          class="fab"
          title="Facturar">
          🧾
        </button>

        @if(isset($credito))
          <a href="{{ route('creditos.show', $credito->id_credito) }}">
            Ver Crédito #{{ $credito->id_credito }}
          </a>
        @endif

        <a
          id="fabLimpiar"
          class="btn-float"
          href="#modalBuscarFactura"
          data-bs-toggle="modal"
          title="Facturas & Auditoría">
          🧾
        </a>

        <a
          href="{{ route('sistema') }}"
          class="back-corner"
          title="Volver a Inicio">
          ←
        </a>

        <a
          href="{{ route('clientes.index') }}"
          class="btn btn-primary btn-float"
          title="Gestión de Clientes">
          👤
        </a>

        <a
          href="{{ route('creditos.index') }}"
          class="btn-float-creditos"
          title="Listado de créditos">
          💳
        </a>

        <button
          type="button"
          class="btn btn-secondary btn-limpiar"
          title="Limpiar"
          onclick="window.location.reload()">
          🧹
        </button>
      </div>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
  <script src="{{ asset('js/Facturacion/FacturaA.js') }}"></script>
    <script src="{{ asset('js/Facturacion/FacturaB.js') }}"></script>
  <script src="{{ asset('js/Facturacion/ModalCompras.js') }}"></script>
  <script src="{{ asset('js/Facturacion/ModalReprintFact.js') }}"></script>

<div class="modal fade" id="modalBuscarFactura" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Facturas & Auditoría</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form
          id="buscarFacturaForm"
          method="GET"
          target="_blank"
          action="#"
          data-reprint-url="{{ route('facturacion.reprint', ['id' => 'REPLACE_ID']) }}"
        >
          <div class="mb-3">
            <label for="numeroFactura" class="form-label">Número de Factura</label>
            <input
              type="number"
              class="form-control"
              id="numeroFactura"
              name="numeroFactura"
              placeholder="Ej. 123"
              required
            >
          </div>
          <button type="submit" class="btn btn-primary w-100 mb-3">
            🖨️ Descargar PDF
          </button>
        </form>
        <hr>
        <form
          action="{{ route('facturacion.audit') }}"
          method="GET"
          target="_blank"
        >
          <p>Generar auditoría de productos :</p>
          <button type="submit" class="btn btn-secondary w-100">
            📑 Descargar Auditoría
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
  <div class="modal fade" id="modalNuevoCliente" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Nuevo Cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" action="{{ route('clientes.store') }}">
          @csrf
          <div class="modal-body">
            <input
              name="nombre"
              type="text"
              class="form-control mb-2"
              placeholder="Nombre"
              required>
            <input
              name="direccion"
              type="text"
              class="form-control mb-2"
              placeholder="Dirección">
            <input
              name="telefono"
              type="text"
              class="form-control mb-2"
              placeholder="Teléfono">
          </div>
          <div class="modal-footer">
            <button class="btn btn-success w-100">➕ Agregar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalRegistrarCompra" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">📦 Registrar Compra</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="compraAlert"></div>
          <form
            id="formRegistrarCompra"
            method="POST"
            action="{{ route('compras.store') }}"
            novalidate
          >
            @csrf

            <h4 class="mb-4 text-success">📦 Registrar Compra</h4>

            <div class="mb-3 text-start">
              <label for="id_proveedor" class="form-label">Proveedor:</label>
              <select
                name="id_proveedor"
                id="id_proveedor"
                class="form-select"
                required
              >
                <option value="">-- Selecciona --</option>
                @foreach($proveedores as $p)
                  <option value="{{ $p->id_proveedor }}">{{ $p->nombre }}</option>
                @endforeach
              </select>
            </div>

            <div class="mb-3 text-start">
              <label class="form-label">Usuario:</label>
              <input
                type="text"
                class="form-control"
                value="{{ auth()->user()->nombre }}"
                disabled
              >
              <input
                type="hidden"
                name="id_usuario"
                value="{{ auth()->user()->id_usuario }}"
              >
            </div>

            <div class="mb-3 text-start">
              <label for="id_producto" class="form-label">Producto:</label>
              <select
                name="id_producto"
                id="id_producto"
                class="form-select"
                required
              >
                <option value="">-- busca un producto --</option>
                @foreach($productos as $prod)
                  <option
                    value="{{ $prod->id_producto }}"
                    data-precio="{{ $prod->precio_venta }}"
                    data-descripcion="{{ $prod->descripcion }}"
                  >
                    {{ $prod->nombre }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="mb-3 text-start">
              <label for="descripcion_producto" class="form-label">
                Descripción del Producto:
              </label>
              <textarea
                id="descripcion_producto"
                class="form-control"
                rows="2"
                readonly
                placeholder="Aquí aparecerá la descripción…"
              ></textarea>
            </div>

            <div class="mb-3 text-start">
              <label for="cantidad" class="form-label">Cantidad:</label>
              <input
                type="number"
                name="cantidad"
                id="cantidad"
                class="form-control"
                min="1"
                required
              >
            </div>

            <div class="mb-3 text-start">
              <label for="precio_unitario" class="form-label">
                Precio unitario (C$):
              </label>
              <input
                type="number"
                name="precio_unitario"
                id="precio_unitario"
                class="form-control"
                step="0.01"
                min="0"
                required
              >
              <div class="form-text">
                No puede ser mayor al precio de venta del producto.
              </div>
            </div>

            <button
              type="submit"
              class="btn btn-success w-100"
              id="btnGuardar"
              disabled
            >
              Guardar Compra
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
