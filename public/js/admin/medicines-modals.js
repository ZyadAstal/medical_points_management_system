(function () {
    'use strict';

    const addModal = document.getElementById('addDrugModal');
    const editModal = document.getElementById('editDrugModal');
    const addBtn = document.getElementById('openAddDrugModal');

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

    window.editMedicine = function (id, name, points, expiry) {
        const form = document.getElementById('editDrugForm');
        if (!form) return;

        form.action = `/superadmin/medicines/${id}`;
        document.getElementById('editDrugName').value = name;
        document.getElementById('editDrugPoints').value = points;
        document.getElementById('e_expiry').value = expiry;

        openModal(editModal);
    };

    // Close buttons logic
    document.addEventListener('click', function (e) {
        const cancelBtn = e.target.closest('.modal-btn-cancel');
        if (cancelBtn) {
            e.preventDefault();
            closeModal(cancelBtn.closest('.medicines-modal-overlay'));
        }
    });

    // Global close on overlay click
    document.querySelectorAll('.medicines-modal-overlay').forEach(over => {
        over.onclick = (e) => {
            if (e.target === over) closeModal(over);
        };
    });
})();
