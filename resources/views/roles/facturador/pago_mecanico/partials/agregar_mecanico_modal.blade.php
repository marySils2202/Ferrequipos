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
