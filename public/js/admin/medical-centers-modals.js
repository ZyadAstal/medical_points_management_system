(function () {
  'use strict';

  const addModal = document.getElementById('mcAddModal');
  const editModal = document.getElementById('mcEditModal');
  const addBtn = document.getElementById('mcAddCenterBtn');

  function openModal(m) {
    if (!m) return;
    m.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }

  function closeModal(m) {
    if (!m) return;
    m.style.display = 'none';
    document.body.style.overflow = '';
  }

  if (addBtn) {
    addBtn.addEventListener('click', function (e) {
      e.preventDefault();
      openModal(addModal);
    });
  }

  window.editCenter = function (id, name, location, phone) {
    const form = document.getElementById('mcEditForm');
    if (!form) return;

    form.action = `/superadmin/centers/${id}`;
    document.getElementById('mcEditName').value = name;
    document.getElementById('mcEditLocation').value = location;
    document.getElementById('mcEditPhone').value = phone;

    openModal(editModal);
  };

  // Close buttons logic
  document.addEventListener('click', function (e) {
    const cancelBtn = e.target.closest('.mc-modal-btn-cancel');
    if (cancelBtn) {
      e.preventDefault();
      closeModal(cancelBtn.closest('.mc-modal-overlay'));
    }
  });

  // Global close on overlay click
  document.querySelectorAll('.mc-modal-overlay').forEach(over => {
    over.onclick = (e) => {
      if (e.target === over) closeModal(over);
    };
  });
})();
