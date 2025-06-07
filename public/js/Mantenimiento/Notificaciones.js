 function toggleAccordion(btn, bodyId) {
      const body = document.getElementById(bodyId);
      const icon = btn.querySelector('.accordion-icon');
      const isOpen = body.classList.toggle('show');
      icon.classList.toggle('rotate', isOpen);
    }
