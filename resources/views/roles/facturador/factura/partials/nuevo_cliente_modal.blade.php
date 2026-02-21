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
