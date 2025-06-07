// public/js/mi-script.js

document.addEventListener('DOMContentLoaded', () => {
  const selectProd      = document.getElementById('id_producto');
  const inputPrecio     = document.getElementById('precio_unitario');
  const btnGuardar      = document.getElementById('btnGuardar');
  const descripcionArea = document.getElementById('descripcion_producto');
  const alertContainer  = document.getElementById('compraAlert');

  // Inicializa TomSelect en el <select> de productos
  const ts = new TomSelect(selectProd, {
    create: false,
    sortField: { field: 'text', direction: 'asc' },
    placeholder: '-- busca un producto --',
    onInitialize() {
      // Deshabilita el botón al iniciar
      btnGuardar.disabled = true;
      // Esperamos un instante a que cargue el select, luego actualizamos descripción si existiera valor
      setTimeout(actualizarDescripcion, 50);
    },
    onChange(value) {
      // Cuando cambie la selección, actualiza descripción y valida precio
      actualizarDescripcion();
      revisarPrecio();
    }
  });

  // Copia el data-descripcion del <option> seleccionado al <textarea>
  function actualizarDescripcion() {
    const valor = ts.getValue() || '';
    if (!valor) {
      descripcionArea.value = '';
      return;
    }
    const opt = selectProd.querySelector(`option[value="${valor}"]`);
    descripcionArea.value = opt?.dataset.descripcion || '';
  }

  // Valida que el precio de compra no sea mayor al precio de venta (data-precio)
  function revisarPrecio() {
    // Limpiamos alertas previas
    alertContainer.innerHTML = '';
    const valorProd   = ts.getValue();
    const compra      = parseFloat(inputPrecio.value) || 0;
    const opt         = selectProd.querySelector(`option[value="${valorProd}"]`);
    const precioVenta = parseFloat(opt?.dataset.precio || 0);

    // Si no hay producto o precio inválido, deshabilitamos el botón
    if (!valorProd || compra <= 0) {
      btnGuardar.disabled = true;
      return;
    }

    // Si el precio de compra excede al precio de venta, mostramos alerta y deshabilitamos
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
    } else {
      btnGuardar.disabled = false;
    }
  }

  // Cada vez que escribes en “precio_unitario”, vuelves a validar
  inputPrecio.addEventListener('input', revisarPrecio);

  // Al cargar la página, actualizamos la descripción en caso de existir valor por defecto
  actualizarDescripcion();
});
