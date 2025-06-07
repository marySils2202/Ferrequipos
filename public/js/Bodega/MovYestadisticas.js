const input        = document.getElementById('searchInput');
  const typeFilter   = document.getElementById('typeFilter');
  const dateFilter   = document.getElementById('dateFilter');
  const toggleAll    = document.getElementById('toggleAll');
  const btnPdf       = document.getElementById('btnPdf');
  const wrapper      = document.querySelector('.table-wrap');
  const toggleStats  = document.getElementById('toggleStats');
  const statsSection = document.getElementById('statsSection');
  const baseUrl = btnPdf.getAttribute('href');
  if (toggleStats && statsSection) {
    toggleStats.addEventListener('click', function() {
      const showing = statsSection.classList.toggle('show');
      this.textContent = showing 
        ? '🚫 Ocultar estadísticas' 
        : '👁️ Mostrar estadísticas';
      this.classList.toggle('btn-primary', showing);
      this.classList.toggle('btn-outline-primary', !showing);
    });
  }

  function updatePdfLink() {
    const params = new URLSearchParams();
    const txt   = input.value.trim();
    const tipo  = typeFilter.value;
    const fecha = dateFilter.value;

    if (txt)   params.set('filtro', txt);
    if (tipo)  params.set('tipo', tipo);
    if (fecha) params.set('fecha', fecha);

    btnPdf.href = params.toString() 
      ? `${baseUrl}?${params}` 
      : baseUrl;
  }

  function filterRows() {
    const txt   = input.value.toLowerCase();
    const tipo  = typeFilter.value;
    const dDate = dateFilter.value;

    document.querySelectorAll('.table-wrap tbody tr').forEach(row => {
      const cols      = row.querySelectorAll('td');
      const textMatch = row.textContent.toLowerCase().includes(txt);
      const tipoMatch = !tipo || cols[3].textContent === tipo;
      const dateMatch = !dDate || cols[1].textContent.split(' ')[0] === dDate;
      row.style.display = (textMatch && tipoMatch && dateMatch) ? '' : 'none';
    });
  }
  function toggleTableWrap() {
    const expanded = wrapper.classList.toggle('expanded');
    wrapper.classList.toggle('collapsed', !expanded);
    toggleAll.textContent = expanded 
      ? '↕ Ocultar exceso' 
      : '↕ Mostrar todo';
  }

  input.addEventListener('input', () => { updatePdfLink(); filterRows(); });
  typeFilter.addEventListener('change', () => { updatePdfLink(); filterRows(); });
  dateFilter.addEventListener('change', () => { updatePdfLink(); filterRows(); });
  toggleAll.addEventListener('click', toggleTableWrap);

  updatePdfLink();
  filterRows();
