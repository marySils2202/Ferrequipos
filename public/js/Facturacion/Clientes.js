
    document.addEventListener('DOMContentLoaded', () => {
      const input    = document.getElementById('filterCliente'),
            rows     = () => document.querySelectorAll('#tablaClientes tbody tr:not(.collapse)');
      input.addEventListener('input', () => {
        const term = input.value.trim().toLowerCase();
        rows().forEach(r => {
          r.style.display = r.cells[1].textContent.toLowerCase().includes(term) ? '' : 'none';
        });
      });

      const toggleAll = document.getElementById('toggleAll'),
            wrap      = document.querySelector('.table-wrap');
      toggleAll.addEventListener('click', () => {
        wrap.classList.toggle('expanded');
        wrap.classList.toggle('collapsed');
        toggleAll.textContent = wrap.classList.contains('expanded')
          ? '↕ Ocultar exceso'
          : '↕ Mostrar todo';
      });
    });
