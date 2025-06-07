document.addEventListener('DOMContentLoaded', () => {
  const tabP = document.getElementById('tabProductos'),
        tabV = document.getElementById('tabProveedores'),
        secP = document.getElementById('sectionProductos'),
        secV = document.getElementById('sectionProveedores');

  tabP.addEventListener('click', () => {
    secP.classList.add('active');
    secV.classList.remove('active');
  });
  tabV.addEventListener('click', () => {
    secV.classList.add('active');
    secP.classList.remove('active');
  });
  tabP.click(); 
  const csrf = document.head.querySelector('meta[name="csrf-token"]').content;

  function showAlert(type, msg) {
    const container = document.getElementById('alert-container');
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = msg + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    container.append(alertDiv);
    setTimeout(() => bootstrap.Alert.getOrCreateInstance(alertDiv).close(), 4000);
  }

  function mkInput(value, tipo = 'text') {
    const inp = document.createElement('input');
    inp.type = tipo;
    inp.value = value;
    inp.className = 'form-control form-control-sm';
    return inp;
  }

  document.querySelectorAll('#tableProductos tbody tr').forEach(row => {
    const btnEdit   = row.querySelector('.btn-edit');
    const btnSave   = row.querySelector('.btn-save');
    const btnCancel = row.querySelector('.btn-cancel');
    const btnDelete = row.querySelector('.btn-delete');


    btnEdit.addEventListener('click', () => {

      const nameText     = row.querySelector('.cell-nombre').textContent.trim();
      const descText     = row.querySelector('.cell-descripcion').textContent.trim();
      const stockText    = row.querySelector('.cell-stock').textContent.trim();
      const priceText    = row.querySelector('.cell-precio').textContent.trim().replace(',', '');
      const categoriaVal = row.dataset.categoria;
      const estadoVal    = row.dataset.estado;

      row.querySelector('.cell-nombre').innerHTML = '';
      row.querySelector('.cell-nombre').appendChild(mkInput(nameText));

      row.querySelector('.cell-descripcion').innerHTML = '';
      row.querySelector('.cell-descripcion').appendChild(mkInput(descText));

      const selCat = document.createElement('select');
      selCat.className = 'form-select form-select-sm';
      selCat.innerHTML = `
        <option value="">--Selecciona--</option>
        ${categoriasOptionsHtml}
      `;
      selCat.value = categoriaVal;
      row.querySelector('.cell-cat').innerHTML = '';
      row.querySelector('.cell-cat').appendChild(selCat);
      const selEst = document.createElement('select');
      selEst.className = 'form-select form-select-sm';
      selEst.innerHTML = `
        <option value="1">Activo</option>
        <option value="0">Inactivo</option>
      `;
      selEst.value = estadoVal;
      row.querySelector('.cell-estado').innerHTML = '';
      row.querySelector('.cell-estado').appendChild(selEst);

      row.querySelector('.cell-stock').innerHTML = '';
      row.querySelector('.cell-stock').appendChild(mkInput(stockText, 'number'));


      const inpPrecio = mkInput(priceText, 'number');
      inpPrecio.step = '0.01';
      inpPrecio.min  = '0';
      row.querySelector('.cell-precio').innerHTML = '';
      row.querySelector('.cell-precio').appendChild(inpPrecio);

      btnEdit.classList.add('d-none');
      btnSave.classList.remove('d-none');
      btnCancel.classList.remove('d-none');
    });

    btnCancel.addEventListener('click', () => window.location.reload());


    btnSave.addEventListener('click', async () => {
      const id               = row.dataset.id;
      const inpNombre        = row.querySelector('.cell-nombre input');
      const inpDescripcion   = row.querySelector('.cell-descripcion input');
      const selCategoria     = row.querySelector('.cell-cat select');
      const selEstado        = row.querySelector('.cell-estado select');
      const inpStock         = row.querySelector('.cell-stock input');
      const inpPrecio        = row.querySelector('.cell-precio input');

      const payload = {
        nombre:        inpNombre.value,
        descripcion:   inpDescripcion.value,
        id_categoria:  selCategoria.value,
        estado:        selEstado.value,
        stock_minimo:  inpStock.value,
        precio_venta:  inpPrecio.value
      };

      try {
        const response = await fetch(`/productos/${id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'Accept':       'application/json'
          },
          body: JSON.stringify(payload)
        });

        if (!response.ok) {
          const errorData = await response.json();
          const msg = errorData.error
                    || (errorData.errors
                          ? Object.values(errorData.errors).flat()[0]
                          : 'Error desconocido');
          throw msg;
        }

        const updated = await response.json();
        row.querySelector('.cell-nombre').textContent      = updated.nombre;
        row.querySelector('.cell-descripcion').textContent = updated.descripcion ?? '';
        row.querySelector('.cell-cat').textContent         = updated.categoria.nombre_categoria;
        row.querySelector('.cell-estado').textContent      = updated.estado ? 'Activo' : 'Inactivo';
        row.querySelector('.cell-stock').textContent       = updated.stock_minimo;
        row.querySelector('.cell-precio').textContent      = Number(updated.precio_venta).toFixed(2);
        row.dataset.estado      = updated.estado ? '1' : '0';
        row.dataset.descripcion = String((updated.descripcion ?? '').toLowerCase());
        row.dataset.categoria   = String(updated.id_categoria);

        if (!updated.estado) {
          showAlert('success', 'Producto marcado inactivo. Recargando...');
          setTimeout(() => window.location.reload(), 600);
          return;
        }

        if (btnDelete) {
          btnDelete.disabled = true;
          btnDelete.classList.replace('btn-outline-danger', 'btn-outline-secondary');
        }

  
        btnSave.classList.add('d-none');
        btnCancel.classList.add('d-none');
        btnEdit.classList.remove('d-none');

        showAlert('success', 'Producto actualizado correctamente.');
      }
      catch (err) {
        showAlert('danger', typeof err === 'string' ? err : 'No se pudo actualizar.');
      }
    });


    if (btnDelete) {
      btnDelete.addEventListener('click', async () => {
        if (!confirm('¿Eliminar este producto?')) return;
        const id = row.dataset.id;
        try {
          const res = await fetch(`/productos/${id}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': csrf,
              'Accept':       'application/json'
            }
          });
          if (!res.ok) throw '';
          showAlert('success', 'Producto eliminado.');
          row.remove();
        }
        catch {
          showAlert('danger', 'No se pudo eliminar.');
        }
      });
    }
  }); 
  const filasP = Array.from(document.querySelectorAll('#tableProductos tbody tr'));
  document.getElementById('searchProducto').addEventListener('input', filtrarProductos);
  document.getElementById('filterCategoria').addEventListener('change', filtrarProductos);

  function filtrarProductos() {
    const term = document.getElementById('searchProducto').value.toLowerCase();
    const cat  = document.getElementById('filterCategoria').value;
    filasP.forEach(fila => {
      const incluyeNombre      = fila.dataset.nombre.includes(term);
      const incluyeDescripcion = fila.dataset.descripcion.includes(term);
      const coincideCategoria  = !cat || fila.dataset.categoria === cat;
      fila.style.display = (coincideCategoria && (incluyeNombre || incluyeDescripcion)) ? '' : 'none';
    });
  }


  const toggleProdBtn = document.getElementById('toggleProductos');
  let showAllProducts = false;

  function actualizarToggleProductos() {
    if (!showAllProducts) {
      filasP.slice(5).forEach(f => f.classList.add('hidden-row'));
      toggleProdBtn.innerHTML = '⇅ Mostrar todo';
    } else {
      filasP.forEach(f => f.classList.remove('hidden-row'));
      toggleProdBtn.innerHTML = '↑↓ Ocultar exceso';
    }
  }

  if (toggleProdBtn) {
    actualizarToggleProductos();
    toggleProdBtn.addEventListener('click', () => {
      showAllProducts = !showAllProducts;
      actualizarToggleProductos();
    });
  }

  const filasV = Array.from(document.querySelectorAll('#tableProveedores tbody tr'));
  document.getElementById('searchProveedor').addEventListener('input', (e) => {
    const termProv = e.target.value.toLowerCase();
    filasV.forEach(f => f.style.display = f.dataset.nombre.includes(termProv) ? '' : 'none');
  });

  const toggleProvBtn = document.getElementById('toggleProveedores');
  let showAllVendors = false;

  function actualizarToggleProveedores() {
    if (!showAllVendors) {
      filasV.slice(5).forEach(f => f.classList.add('hidden-row'));
      toggleProvBtn.innerHTML = '⇅ Mostrar todo';
    } else {
      filasV.forEach(f => f.classList.remove('hidden-row'));
      toggleProvBtn.innerHTML = '↑↓ Ocultar exceso';
    }
  }

  if (toggleProvBtn) {
    actualizarToggleProveedores();
    toggleProvBtn.addEventListener('click', () => {
      showAllVendors = !showAllVendors;
      actualizarToggleProveedores();
    });
  }
});
