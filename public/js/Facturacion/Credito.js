
    function reembolsar(btn) {
      const id = btn.dataset.credito;
      if (confirm(`¿Seguro que deseas anular el crédito #${id} y reembolsar productos?`)) {
        document.getElementById(`formReembolso-${id}`).submit();
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      const alertDiv      = document.getElementById('globalAlert'),
            carrito       = [],
            tabla         = document.getElementById('carritoTabla'),
            tbody         = tabla.querySelector('tbody'),
            totalCell     = document.getElementById('totalCell'),
            realizarBtn   = document.getElementById('realizarBtn'),
            clienteInput  = document.getElementById('clienteInput'),
            clienteHidden = document.getElementById('clienteHidden'),
            listaCli      = [...document.querySelectorAll('#clientesList option')],
            prodInput     = document.getElementById('productoInput'),
            listaInv      = [...document.querySelectorAll('#inventariosList option')],
            descArea      = document.getElementById('descripcionProducto'),
            cantInput     = document.getElementById('cantidadInput'),
            addBtn        = document.getElementById('addBtn'),
            abonoModal    = document.getElementById('abonoModal'),
            montoInput    = document.getElementById('montoInput'),
            abonoForm     = document.getElementById('abonoForm'),
            formNuevoCli  = document.getElementById('formNuevoCliente'),
            modalNuevoCli = document.getElementById('modalNuevoCliente');
      clienteInput.addEventListener('input', () => {
        const val = clienteInput.value.trim();
        const opt = listaCli.find(o => o.value === val);
        clienteHidden.value = opt ? opt.dataset.id : '';
      });
      formNuevoCli.addEventListener('submit', async e => {
        e.preventDefault();
        const formData = new FormData(formNuevoCli);
        try {
          const res = await fetch(formNuevoCli.action, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
              'Accept':       'application/json'
            },
            body: formData
          });
          if (!res.ok) throw new Error('Error al crear cliente');
          const data = await res.json();
          const opt = document.createElement('option');
          opt.value      = data.nombre;
          opt.dataset.id = data.id_cliente;
          document.getElementById('clientesList').append(opt);
          listaCli.push(opt);
          clienteInput.value  = data.nombre;
          clienteHidden.value = data.id_cliente;
          bootstrap.Modal.getInstance(modalNuevoCli).hide();
          formNuevoCli.reset();
        } catch(msg) {
          alert(msg);
        }
      });
      prodInput.addEventListener('input', () => {
        const val = prodInput.value.trim();
        const opt = listaInv.find(o => o.value === val);
        if (opt) {
          descArea.value = opt.dataset.descripcion || '';
        } else {
          descArea.value = '';
        }
      });
      addBtn.addEventListener('click', () => {
        alertDiv.innerHTML = '';
        const cliName = clienteInput.value.trim();
        const cliOpt  = listaCli.find(o => o.value === cliName);
        if (!cliOpt) {
          return alertDiv.innerHTML = `<div class="alert alert-danger">
            Cliente "${cliName}" no existe.
          </div>`;
        }
        clienteHidden.value = cliOpt.dataset.id;
        const valor = prodInput.value.trim();
        const opt   = listaInv.find(o => o.value === valor);
        if (!opt) {
          return alertDiv.innerHTML = `<div class="alert alert-danger">
            Producto "${valor}" no existe.
          </div>`;
        }

        const stock = parseInt(opt.dataset.stock, 10);
        let cant    = parseInt(cantInput.value, 10) || 1;
        if (cant < 1) cant = 1;
        const existing = carrito.find(item => item.id === opt.dataset.id);

        if (existing) {
          if (existing.cant + cant > stock) {
            return alertDiv.innerHTML = `<div class="alert alert-warning">
              Sólo hay ${stock} unidades disponibles.
            </div>`;
          }
          existing.cant += cant;
          existing.sub  = existing.precio * existing.cant;
        } else {
          if (cant > stock) {
            return alertDiv.innerHTML = `<div class="alert alert-warning">
              Sólo hay ${stock} unidades disponibles.
            </div>`;
          }
          const precio      = parseFloat(opt.dataset.precio);
          const descripcion = opt.dataset.descripcion || '';

          carrito.push({
            id:          opt.dataset.id,
            nombre:      opt.dataset.nombre,
            descripcion: descripcion,
            precio:      precio,
            cant:        cant,
            sub:         precio * cant
          });
        }

        prodInput.value = '';
        descArea.value  = '';
        cantInput.value = 1;
        renderCarrito();
      });
      window.removeItem = i => {
        carrito.splice(i, 1);
        renderCarrito();
      };
      function renderCarrito() {
        tbody.innerHTML = '';
        let total = 0;
        carrito.forEach((it, i) => {
          total += it.sub;
          tbody.insertAdjacentHTML('beforeend', `
            <tr>
              <td>
                <input type="hidden"
                       name="productos[${i}][id_producto]"
                       value="${it.id}">
                ${it.nombre}
              </td>
              <td>${it.descripcion}</td>
              <td>
                <input type="hidden"
                       name="productos[${i}][cantidad]"
                       value="${it.cant}">
                ${it.cant}
              </td>
              <td>${it.precio.toFixed(2)}</td>
              <td>${it.sub.toFixed(2)}</td>
              <td>
                <button type="button"
                        class="btn btn-circle btn-danger"
                        onclick="removeItem(${i})">×</button>
              </td>
            </tr>
          `);
        });
        totalCell.textContent = total.toFixed(2);
        tabla.classList.toggle('d-none', carrito.length === 0);
        realizarBtn.classList.toggle('d-none', carrito.length === 0);
      }
      document.getElementById('carritoForm')
        .addEventListener('submit', e => {
          if (carrito.length === 0) {
            e.preventDefault();
            alertDiv.innerHTML = `<div class="alert alert-danger">
              Añade al menos un producto.
            </div>`;
          }
        });
      abonoModal.addEventListener('show.bs.modal', e => {
        const btn = e.relatedTarget;
        abonoForm.action = `/creditos/${btn.dataset.idc}/abonar`;
        montoInput.value = btn.dataset.saldo;
      });

    });
