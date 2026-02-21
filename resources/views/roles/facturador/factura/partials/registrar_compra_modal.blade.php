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
