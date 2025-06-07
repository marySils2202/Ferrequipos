document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('buscarFacturaForm');
  const inputFactura = document.getElementById('numeroFactura');

  form.addEventListener('submit', function(e) {
    e.preventDefault();

    const idFactura = inputFactura.value.trim();
    if (!idFactura) {
      return alert('Ingresa un número de factura.');
    }
    const baseUrl = form.dataset.reprintUrl; 
    const url = baseUrl.replace('REPLACE_ID', idFactura);

    window.open(url, '_blank');
  });
});
