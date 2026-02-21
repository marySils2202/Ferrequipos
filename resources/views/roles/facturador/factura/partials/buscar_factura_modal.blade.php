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
