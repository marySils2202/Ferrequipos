document.addEventListener('DOMContentLoaded', () => {
  const formFact     = document.getElementById('facturaForm'),
        clienteInput = document.querySelector('.cliente-input'),
        clienteIdEl  = document.querySelector('.cliente-id'),
        listaCli     = [...document.querySelectorAll('#clientesList option')],
        globalAlert  = document.getElementById('globalAlert'),
        lineas       = document.getElementById('lineas'),
        descuentoEl  = document.getElementById('descuento'),
        submitBtn    = formFact.querySelector('button[type="submit"]'),
        listaProd    = [...document.querySelectorAll('#productosList option')],
        moGen        = document.getElementById('moGen'),
        subTot       = document.getElementById('subTot'),
        tot          = document.getElementById('tot'),
        pagoVis      = document.getElementById('pagoVis'),
        vuelto       = document.getElementById('vuelto'),
        pagoHidden   = document.getElementById('pago'),
        metodoPago   = document.getElementById('metodoPago');

  // Función para mostrar alertas temporales
  function flash(html) {
    globalAlert.insertAdjacentHTML('beforeend', html);
    const last = globalAlert.lastElementChild;
    setTimeout(() => last?.remove(), 3000);
  }

  // Recalcula el subtotal en una fila (<tr>)
  function recLinea(tr) {
    const precio   = parseFloat(tr.querySelector('.prec').value) || 0;
    const cantidad = parseInt(tr.querySelector('.cant').value)   || 0;
    tr.querySelector('.sub').value = (precio * cantidad).toFixed(2);
  }

  // Recalcula subtotales, totales y vuelto, considerando descuento y mano de obra
  function recTotales() {
    let sumaProd = 0;
    document.querySelectorAll('.sub').forEach(i => {
      sumaProd += parseFloat(i.value) || 0;
    });

    const mo       = parseFloat(moGen.value)       || 0;
    const desc     = parseFloat(descuentoEl.value) || 0;
    const totalNet = sumaProd + mo - desc;
    const pagoNum  = parseFloat(pagoVis.value)     || 0;

    subTot.value     = sumaProd.toFixed(2);
    tot.value        = totalNet.toFixed(2);
    vuelto.value     = (pagoNum - totalNet).toFixed(2);
    pagoHidden.value = pagoNum.toFixed(2);
  }

  // Valida que el pago cubra el total (o se permita “crédito”)
  function validatePago() {
    globalAlert.querySelectorAll('[data-context="pago"]').forEach(x => x.remove());

    const totalReq = parseFloat(tot.value)   || 0;
    const pagoNum  = parseFloat(pagoVis.value) || 0;
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
    globalAlert.querySelectorAll('[data-context="stock"]').forEach(x => x.remove());
    flash(`
      <div class="alert alert-warning" data-context="stock" role="alert">
        Sólo hay <strong>${max}</strong> unidades disponibles.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>`);
  }

  // Cuando el usuario escribe en “Cliente”, sincroniza el ID oculto
  clienteInput.addEventListener('input', () => {
    const opt = listaCli.find(o => o.value === clienteInput.value.trim());
    clienteIdEl.value = opt ? opt.dataset.id : '';
  });

  // Al enviar el formulario principal de factura, validaciones de cliente y stock
  formFact.addEventListener('submit', e => {
    if (!clienteIdEl.value) {
      e.preventDefault();
      alert('Selecciona un cliente válido antes de facturar.');
      clienteInput.focus();
      return;
    }
    if (globalAlert.querySelector('[data-context="stock"]')) {
      e.preventDefault();
      alert('Ajusta primero la cantidad al stock disponible.');
    }
  });

  // Maneja interacción dentro de las filas de la tabla de productos
  lineas.addEventListener('input', e => {
    const tr = e.target.closest('tr');
    if (!tr) return;

    // Si cambió la cantidad
    if (e.target.matches('.cant')) {
      const want = parseInt(e.target.value, 10) || 0;
      const max  = parseInt(tr.dataset.stock, 10) || 0;
      if (want > max) {
        e.target.value = max;
        showStockAlert(max);
      }
    }

    // Si cambió el campo de producto (autocomplete)
    if (e.target.matches('.prod-input')) {
      globalAlert.querySelectorAll('[data-context="producto"]').forEach(x => x.remove());
      const valor   = e.target.value.trim();
      const optProd = listaProd.find(o => o.value === valor);
      const precioEl= tr.querySelector('.prec');
      const qtyEl   = tr.querySelector('.cant');
      const descEl  = tr.querySelector('.desc');

      if (!optProd) {
        // Producto NO existe en la lista
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
        // Verificar si ya agregué ese producto en alguna fila
        const pid   = optProd.dataset.id;
        const count = [...document.querySelectorAll('.prod-id')]
                      .filter(i => i.value === pid).length;
        if (count > 1) {
          // Si ya está agregado en otra fila
          e.target.value = '';
          flash(`
            <div class="alert alert-warning" data-context="producto" role="alert">
              El producto "<strong>${valor}</strong>" ya ha sido agregado.
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`);
        } else {
          // Rellenar ID, precio, descripción y stock
          tr.querySelector('.prod-id').value = pid;
          precioEl.value = parseFloat(optProd.dataset.precio || 0).toFixed(2);
          descEl.value   = optProd.dataset.descripcion || '';
          tr.dataset.stock = optProd.dataset.stock;

          // Si stock = 0, forzar cantidad a cero
          if (parseInt(optProd.dataset.stock, 10) === 0) {
            qtyEl.value = 0;
            showStockAlert(0);
          }
        }
      }
    }

    // Después de cualquier cambio en la fila, recalcular subtotal de fila y totales generales
    recLinea(tr);
    recTotales();
    // Limpiar el campo de pago visible para evitar inconsistencias
    pagoVis.value = '';
    validatePago();
  });

  // Manejo de botones “Agregar fila” y “Eliminar fila”
  document.addEventListener('click', e => {
    // Agregar una nueva fila (solo se clona la estructura, no contenido)
    if (e.target.matches('.add')) {
      const src = e.target.closest('tr');
      const nw  = src.cloneNode(true);

      // Limpiar todos los inputs de la nueva fila
      nw.querySelectorAll('input').forEach(i => {
        if (i.classList.contains('cant')) {
          i.value = 1;
        } else {
          i.value = '';
        }
      });

      // Asegurarse de reiniciar descripción, precio y subtotal en la nueva fila
      ['.prod-id', '.desc', '.prec', '.sub'].forEach(sel => {
        const inp = nw.querySelector(sel);
        if (inp) {
          inp.value = (sel === '.sub') ? '0.00' : '';
        }
      });

      delete nw.dataset.stock;
      lineas.appendChild(nw);
    }

    // Eliminar una fila (si quedan al menos 2 filas)
    if (e.target.matches('.del') && lineas.querySelectorAll('tr').length > 1) {
      e.target.closest('tr').remove();
    }

    // Cada vez que agregamos/eliminamos fila, recalcular totales y validar pago
    recTotales();
    validatePago();
  });

  [descuentoEl, moGen, pagoVis, metodoPago].forEach(el => {
    el.addEventListener('input', () => {
      recTotales();
      validatePago();
    });
  });

  recTotales();
  validatePago();
});
