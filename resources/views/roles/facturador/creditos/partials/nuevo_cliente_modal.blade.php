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
