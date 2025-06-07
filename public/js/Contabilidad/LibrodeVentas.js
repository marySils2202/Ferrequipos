 document.getElementById('btnFiltrar').addEventListener('click', function() {
      var filtro = document.getElementById('fechaFiltro').value;
      document.querySelectorAll('#tablaLibro tbody tr').forEach(function(tr) {
        var td = tr.querySelector('td[data-fecha]');
        if (!td) return;
        var fecha = td.getAttribute('data-fecha');
        tr.style.display = (!filtro || fecha === filtro) ? '' : 'none';
      });
    });
    var toggleBtn = document.getElementById('toggleTabla');
    var wrap = document.getElementById('tablaWrap');
    toggleBtn.addEventListener('click', function() {
      wrap.classList.toggle('expanded');
      wrap.classList.toggle('collapsed');
      toggleBtn.textContent = wrap.classList.contains('expanded') ? '↕ Ocultar exceso' : '↕ Mostrar todo';
    });