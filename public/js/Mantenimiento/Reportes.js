
    document.getElementById('tipoReporte').addEventListener('change', function(){
      const fecha = document.getElementById('fechaFiltro'),
            filtrosCred = document.getElementById('filtrosCreditos');

      if (['facturas','compras'].includes(this.value)) {
        fecha.removeAttribute('disabled');
        fecha.setAttribute('required','');
        fecha.classList.remove('disabled');
      } else {
        fecha.removeAttribute('required');
        fecha.setAttribute('disabled','');
        fecha.classList.add('disabled');
        fecha.value = '';
      }

      if (this.value === 'creditos') {
        filtrosCred.classList.remove('d-none');
      } else {
        filtrosCred.classList.add('d-none');
        filtrosCred.querySelectorAll('input, select').forEach(i=>i.value = '');
      }
    });

    document.getElementById('btnClear').addEventListener('click', function() {
      window.location.href = "{{ route('filtros.index') }}";
    });