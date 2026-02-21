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
