(function () {
    // Patient search filter in sidebar
    const searchInput = document.getElementById('recipesPatientSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = searchInput.value.trim().toLowerCase();
            document.querySelectorAll('.patient-btn').forEach(function (btn) {
                btn.style.display = btn.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }

    // Modal elements
    const overlay = document.getElementById('rxModalOverlay');
    const closeBtn = document.getElementById('rxModalClose');
    const closeBtnFooter = document.getElementById('rxModalCloseBtn');

    function openModal(btn) {
        const mDate = document.getElementById('rxModalDate');
        const mPatient = document.getElementById('rxModalPatient');
        const mPharmacist = document.getElementById('rxModalPharmacist');
        const mCenter = document.getElementById('rxModalCenter');
        const mItems = document.getElementById('rxModalItems');

        if (mDate) mDate.textContent = btn.dataset.date;
        if (mPatient) mPatient.textContent = btn.dataset.patient;
        if (mPharmacist) mPharmacist.textContent = btn.dataset.pharmacist;
        if (mCenter) mCenter.textContent = btn.dataset.center;

        // Populate items table
        if (mItems && btn.dataset.items) {
            const items = JSON.parse(btn.dataset.items);
            mItems.innerHTML = '';
            items.forEach(function(item, i) {
                const tr = document.createElement('tr');
                tr.innerHTML =
                    '<td style="text-align:center; color:#94a3b8; font-weight:700;">' + (i + 1) + '</td>' +
                    '<td style="font-weight:600;">' + item.medicine + '</td>' +
                    '<td style="text-align:center;">' + item.quantity + '</td>' +
                    '<td style="text-align:center;">' +
                        (item.dispensed
                            ? '<span class="rx-status-dispensed">✓ مصروف</span>'
                            : '<span class="rx-status-pending">✗ غير مصروف</span>') +
                    '</td>';
                mItems.appendChild(tr);
            });
        }

        // Notes
        const notes = btn.dataset.notes;
        const notesDiv = document.getElementById('rxModalNotes');
        const notesText = document.getElementById('rxModalNotesText');
        if (notes && notes.trim() && notesDiv && notesText) {
            notesText.textContent = notes;
            notesDiv.style.display = 'block';
        } else if (notesDiv) {
            notesDiv.style.display = 'none';
        }

        // Show modal
        if (overlay) {
            overlay.classList.add('open');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal() {
        if (overlay) {
            overlay.classList.remove('open');
            document.body.style.overflow = '';
        }
    }

    // Open buttons
    document.querySelectorAll('.recipe-open-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openModal(btn);
        });
    });

    // Close handlers
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (closeBtnFooter) closeBtnFooter.addEventListener('click', closeModal);

    // Close on overlay click
    if (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closeModal();
        });
    }

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay && overlay.classList.contains('open')) closeModal();
    });
})();
