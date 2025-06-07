
    document.getElementById('btnFiltrar').addEventListener('click', () => {
      const filtro = document.getElementById('fechaFiltro').value;
      document.querySelectorAll('#tablaArqueos tbody tr').forEach(tr => {
        const td = tr.querySelector('td[data-fecha]');
        tr.style.display = (!filtro || td.getAttribute('data-fecha') === filtro) ? '' : 'none';
      });
    });

    const toggleBtn = document.getElementById('toggleTabla');
    const wrap      = document.getElementById('historialWrap');
    toggleBtn.addEventListener('click', () => {
      wrap.classList.toggle('expanded');
      wrap.classList.toggle('collapsed');
      toggleBtn.textContent = wrap.classList.contains('expanded')
        ? '↕ Ocultar exceso'
        : '↕ Mostrar todo';
    });
