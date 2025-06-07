
document.addEventListener('DOMContentLoaded', () => {
  const modalEl = document.getElementById('modalMain');
  const modal   = new bootstrap.Modal(modalEl);

  function iniciarTomSelectYValidacion() {
    const selectProd      = document.getElementById('id_producto');
    const inputPrecio     = document.getElementById('precio_unitario');
    const btnGuardar      = document.querySelector('form#formRegistrarCompra button[type="submit"]');
    const descripcionArea = document.getElementById('descripcion_producto');
    const alertContainer  = document.getElementById('compraAlert');
    const precioVisible   = document.getElementById('precio_visible');

    if (!selectProd || !inputPrecio || !btnGuardar || !descripcionArea) return;
    selectProd.setAttribute('name', 'id_producto');
    if (window.tsProducto) {
      window.tsProducto.destroy();
    }
    window.tsProducto = new TomSelect(selectProd, {
      create: false,
      sortField: { field: "text", direction: "asc" },
      placeholder: '-- busca un producto --',
      onInitialize() {
        btnGuardar.disabled = true; 
      },
      onChange(value) {
        validarPrecio(value);
        actualizarDescripcion(value);
        actualizarPrecioVisible(value);
      }
    });

    function actualizarDescripcion(selectedValue) {
      if (!selectedValue) {
        descripcionArea.value = "";
        return;
      }
      const opt   = selectProd.querySelector(`option[value="${selectedValue}"]`);
      const desc  = opt?.dataset.descripcion ?? "";
      descripcionArea.value = desc;
    }


    function validarPrecio(selectedValue) {
    
      alertContainer.innerHTML = "";

      const compra = parseFloat(inputPrecio.value) || 0;
      const opt    = selectProd.querySelector(`option[value="${selectedValue}"]`);
      const venta  = parseFloat(opt?.dataset.precio || 0);

      if (!selectedValue || compra <= 0) {
        btnGuardar.disabled = true;
        return;
      }

      if (compra > venta) {
        const divAlert = document.createElement('div');
        divAlert.className = 'alert alert-danger alert-dismissible fade show';
        divAlert.role = 'alert';
        divAlert.innerHTML = `
          🚫 El precio de compra (${compra.toFixed(2)}) no puede ser mayor
          que el precio de venta (${venta.toFixed(2)}).
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        alertContainer.appendChild(divAlert);
        btnGuardar.disabled = true;
        return;
      }

      btnGuardar.disabled = false;
    }

    function actualizarPrecioVisible(selectedValue) {
      if (!precioVisible) return;
      const opt    = selectProd.querySelector(`option[value="${selectedValue}"]`);
      const precio = opt?.dataset.precio ?? '';
      if (precio) {
        precioVisible.value = `$${parseFloat(precio).toFixed(2)}`;
        inputPrecio.value = precio;
      } else {
        precioVisible.value = '';
        inputPrecio.value = '';
      }

      validarPrecio(selectedValue);
    }

    inputPrecio.addEventListener('input', () => {
      validarPrecio(window.tsProducto.getValue());
    });

    const oldProd = selectProd.getAttribute('data-old') || "";
    if (oldProd) {
      window.tsProducto.setValue(oldProd);
      actualizarDescripcion(oldProd);
      actualizarPrecioVisible(oldProd);
      validarPrecio(oldProd);
    }
  }

  async function openModal(ruta, title) {
    const res  = await fetch(ruta);
    const html = await res.text();

    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalBody').innerHTML    = html;
    modal.show();

    iniciarTomSelectYValidacion();
    attachCompraHandler();
    iniciarSelectorPrecio();
  }

  document.querySelectorAll('.btn-modal').forEach(btn =>
    btn.addEventListener('click', () => openModal(btn.dataset.ruta, btn.textContent.trim()))
  );

  function attachCompraHandler() {
    const form = document.getElementById('formRegistrarCompra');
    if (!form) return;
    form.removeEventListener('submit', compraSubmit);
    form.addEventListener('submit', compraSubmit);
  }

  async function compraSubmit(e) {
    e.preventDefault();
    const form   = e.target;
    const fd     = new FormData(form);
    const alertC = document.getElementById('compraAlert');
    alertC.innerHTML = '';

    const token = document.querySelector('meta[name="csrf-token"]').content;
    const res   = await fetch(form.action, {
      method: form.method,
      headers: {
        'X-CSRF-TOKEN':     token,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept':           'application/json'
      },
      body: fd
    });

    if (res.ok) {
      const { message } = await res.json();
      showAlert('compraAlert', message, 'success');
      form.reset();
      if (window.tsProducto) {
        window.tsProducto.clear(true);
      }
      const precioVisible = document.getElementById('precio_visible');
      if (precioVisible) precioVisible.value = '';
      const btnG = form.querySelector('button[type="submit"]');
      if (btnG) btnG.disabled = true;
    }
    else if (res.status === 422) {
      const err = await res.json();
      showAlert('compraAlert',
        '<ul>' + Object.values(err.errors).flat().map(m => `<li>${m}</li>`).join('') + '</ul>',
        'danger'
      );
    } else {
      console.error('Error inesperado en compra:', res.status);
    }
  }
  function iniciarSelectorPrecio() {
    const select       = document.getElementById('id_producto');
    const vis          = document.getElementById('precio_visible');
    const hiddenPrecio = document.getElementById('precio_unitario');
    if (!select || !vis || !hiddenPrecio) return;

    function actualizar() {
      const precio = select.selectedOptions[0]?.dataset.precio;
      if (precio) {
        vis.value          = `$${parseFloat(precio).toFixed(2)}`;
        hiddenPrecio.value = precio;
      } else {
        vis.value          = '';
        hiddenPrecio.value = '';
      }
    }

    select.addEventListener('change', actualizar);
    actualizar();
  }
  function showAlert(containerId, msg, type) {
    const cont = document.getElementById(containerId);
    const div  = document.createElement('div');
    div.className = `alert alert-${type} alert-dismissible fade show mt-2`;
    div.innerHTML = `
      ${msg}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    cont.appendChild(div);
  }
});
