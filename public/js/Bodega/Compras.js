  document.addEventListener('DOMContentLoaded', () => {
    const selectProd       = document.getElementById('id_producto');
    const inputPrecio      = document.getElementById('precio_unitario');
    const btnGuardar       = document.getElementById('btnGuardar');
    const descripcionArea  = document.getElementById('descripcion_producto');
    const alertContainer   = document.getElementById('compraAlert');
    const ts = new TomSelect(selectProd, {
      create: false,
      sortField: { field: 'text', direction: 'asc' },
      placeholder: '-- busca un producto --',
      onInitialize() {
        btnGuardar.disabled = true;
      }
    });
    function actualizarDescripcion() {
      const selectedValue = ts.getValue();
      if (!selectedValue) {
        descripcionArea.value = '';
        return;
      }
      const opt = selectProd.querySelector(`option[value="${selectedValue}"]`);
      const desc = opt?.dataset.descripcion ?? '';
      descripcionArea.value = desc;
      descripcionArea.setAttribute('data-old', desc);
    }

    function revisarPrecio() {
      alertContainer.innerHTML = '';

      const selectedValue = ts.getValue();
      const compra = parseFloat(inputPrecio.value) || 0;
      const opt = selectProd.querySelector(`option[value="${selectedValue}"]`);
      const precioVenta = parseFloat(opt?.dataset.precio || 0);
      if (!selectedValue || compra <= 0) {
        btnGuardar.disabled = true;
        return;
      }
      if (compra > precioVenta) {
        const divAlert = document.createElement('div');
        divAlert.className = 'alert alert-danger alert-dismissible fade show';
        divAlert.role = 'alert';
        divAlert.innerHTML = `
          🚫 El precio de compra (${compra.toFixed(2)}) no puede ser mayor
          que el precio de venta (${precioVenta.toFixed(2)}).
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        alertContainer.appendChild(divAlert);
        btnGuardar.disabled = true;
        return;
      }
      btnGuardar.disabled = false;
    }

    ts.on('change', () => {
      actualizarDescripcion();
      revisarPrecio();
    });

    inputPrecio.addEventListener('input', revisarPrecio);
    const oldProd = "{{ old('id_producto') }}";
    if (oldProd) {
      ts.setValue(oldProd);      
      actualizarDescripcion();  
      revisarPrecio();       
    }
  });
