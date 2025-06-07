
 const toggleAll = document.getElementById('toggleAll');
    const wrapper   = document.querySelector('.table-wrap');
    toggleAll.addEventListener('click', () => {
      wrapper.classList.toggle('expanded');
      wrapper.classList.toggle('collapsed');
      toggleAll.textContent = wrapper.classList.contains('expanded')
        ? '↕ Ocultar exceso'
        : '↕ Mostrar todo';
    });
