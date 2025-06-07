document.addEventListener('DOMContentLoaded', () => {
  const globalAlert  = document.getElementById('globalAlert');
  const lineas       = document.getElementById('lineas');
  const formFact     = document.getElementById('facturaForm');
  const submitBtn    = formFact.querySelector('button[type="submit"]');
  const listaProd    = [...document.querySelectorAll('#productosList option')];
  const moGen        = document.getElementById('moGen');
  const subTot       = document.getElementById('subTot');
  const tot          = document.getElementById('tot');
  const pagoVis      = document.getElementById('pagoVis');
  const vuelto       = document.getElementById('vuelto');
  const pagoHidden   = document.getElementById('pago');
  const metodoPago   = document.getElementById('metodoPago');

  // Muestra un mensaje flash en el contenedor de alertas globales
  function flash(html) {
    globalAlert.insertAdjacentHTML('beforeend', html);
    const a = globalAlert.lastElementChild;
    setTimeout(() => a?.remove(), 3000);
  }

  // Recalcula el subtotal de una fila (<tr>)
  function recLinea(tr) {
    const precio   = parseFloat(tr.querySelector('.prec').value) || 0;
    const cantidad = parseInt(tr.querySelector('.cant').value)   || 0;
    tr.querySelector('.sub').value = (precio * cantidad).toFixed(2);
  }

  // Recalcula subtotales, totales y vuelto
  function recTotales() {
    let suma = 0;
    document.querySelectorAll('.sub').forEach(i => {
      suma += parseFloat(i.value) || 0;
    });
    const mo       = parseFloat(moGen.value)   || 0;
    const totalReq = suma + mo;
    const pagoNum  = parseFloat(pagoVis.value) || 0;

    subTot.value       = suma.toFixed(2);
    tot.value          = totalReq.toFixed(2);
    vuelto.value       = (pagoNum - totalReq).toFixed(2);
    pagoHidden.value   = pagoNum.toFixed(2);
  }

  // Valida que el monto pagado cubra el total (o bien sea “crédito”)
  function validatePago() {
    // Remueve alertas previas relacionadas con pago
    document.querySelectorAll('[data-context="pago"]').forEach(x => x.remove());

    const subtotal = parseFloat(subTot.value) || 0;
    const mo       = parseFloat(moGen.value)   || 0;
    const pagoNum  = parseFloat(pagoVis.value) || 0;
    const totalReq = subtotal + mo;
    const esCred   = metodoPago.value === 'credito';

    if (esCred || pagoNum >= totalReq) {
      submitBtn.disabled = false;
    } else {
      submitBtn.disabled = true;
      flash(`
        <div class="alert alert-danger" data-context="pago" role="alert">
          Debes pagar al menos C$ ${totalReq.toFixed(2)}.
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`);
    }
  }

  // Muestra advertencia si la cantidad excede el stock disponible
  function showStockAlert(max) {
    document.querySelectorAll('[data-context="stock"]').forEach(x => x.remove());
    flash(`
      <div class="alert alert-warning" data-context="stock" role="alert">
        Sólo hay <strong>${max}</strong> unidades disponibles.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>`);
  }

  // Escucha cambios en cualquier input dentro de las filas de la tabla de línea de factura
  lineas.addEventListener('input', e => {
    const tr = e.target.closest('tr');
    if (!tr) return;

    // Si se modifica la cantidad (“.cant”), asegurar que no supere stock
    if (e.target.matches('.cant')) {
      const want = parseInt(e.target.value, 10) || 0;
      const max  = parseInt(tr.dataset.stock, 10) || 0;
      if (want > max) {
        e.target.value = max;
        showStockAlert(max);
      }
    }

    // Si se escribe/select en el campo de producto (“.prod-input”)
    if (e.target.matches('.prod-input')) {
      document.querySelectorAll('[data-context="producto"]').forEach(x => x.remove());

      const valor   = e.target.value.trim();
      const optProd = listaProd.find(o => o.value === valor);
      const precioEl= tr.querySelector('.prec');
      const descEl  = tr.querySelector('.desc');
      const qtyEl   = tr.querySelector('.cant');

      if (!optProd) {
        // Producto no existe: limpiar campos y mostrar alerta
        tr.querySelector('.prod-id').value = '';
        precioEl.value = '';
        descEl.value   = '';
        delete tr.dataset.stock;

        flash(`
          <div class="alert alert-danger" data-context="producto" role="alert">
            Producto "<strong>${valor}</strong>" no existe.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>`);
      } else {
        // Verificar si ya agregué ese producto en otra fila
        const pid = optProd.dataset.id;
        const count = [...document.querySelectorAll('.prod-id')]
                      .filter(i => i.value === pid).length;
        if (count > 0) {
          // Si ya existe, limpiar input y advertir
          e.target.value = '';
          flash(`
            <div class="alert alert-warning" data-context="producto" role="alert">
              Ya agregaste "<strong>${valor}</strong>".
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`);
        } else {
          // Rellenar ID, precio, descripción y stock
          tr.querySelector('.prod-id').value = pid;
          precioEl.value                    = parseFloat(optProd.dataset.precio || 0).toFixed(2);
          descEl.value                      = optProd.dataset.descripcion || '';
          tr.dataset.stock                  = optProd.dataset.stock;

          // Si no hay stock, forzar cantidad a 0 y mostrar alerta
          if (parseInt(optProd.dataset.stock, 10) === 0) {
            qtyEl.value = 0;
            showStockAlert(0);
          }
        }
      }
    }

    // Después de cualquier cambio en la fila, recalcular subtotal de la fila y totales generales
    recLinea(tr);
    recTotales();
    validatePago();
  });

  // Antes de enviar el formulario, si hay alerta de stock, impedir envío
  formFact.addEventListener('submit', e => {
    if (globalAlert.querySelector('[data-context="stock"]')) {
      e.preventDefault();
      alert('Ajusta primero la cantidad al stock disponible.');
    }
  });

  // Cada vez que cambian Mano de Obra, Monto Pago o Método, volvemos a calcular totales y validar pago
  [moGen, pagoVis, metodoPago].forEach(el => {
    el.addEventListener('input', () => {
      recTotales();
      validatePago();
    });
  });

  // Inicialmente recalculamos totales (en caso de valores cargados por el servidor)
  recTotales();
});
