(function () {
    // Date picker icon
    const input = document.getElementById('todayDate');
    const icon = document.getElementById('todayDateIcon');
    const wrapper = document.querySelector('.today-patients-wrapper');

    if (input && icon) {
        function openPicker() {
            if (typeof input.showPicker === 'function') {
                input.showPicker();
            } else {
                input.focus();
                input.click();
            }
        }
        icon.addEventListener('click', openPicker);
        icon.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openPicker();
            }
        });

        // Reload page with selected date
        input.addEventListener('change', function () {
            const date = input.value;
            if (date && wrapper) {
                const indexUrl = wrapper.dataset.indexUrl;
                window.location.href = indexUrl + '?date=' + date;
            }
        });
    }

    const searchInput = document.getElementById('todaySearchInput');
    const searchBtn = document.getElementById('todaySearchBtn');

    function performSearch() {
        if (!searchInput) return;
        const q = searchInput.value.trim().toLowerCase();
        const rows = document.querySelectorAll('.patients-tr');
        rows.forEach(function (row) {
            const nameTd = row.querySelector('.patients-td:nth-child(1)');
            const nidTd = row.querySelector('.patients-td:nth-child(2)');
            if (!nameTd || !nidTd) return;
            const match = nameTd.textContent.toLowerCase().includes(q) || nidTd.textContent.includes(q);
            row.style.display = match ? '' : 'none';
        });
    }

    if (searchInput && searchBtn) {
        searchBtn.addEventListener('click', performSearch);
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch();
            }
        });
    }

    // Modal
    const modalOverlay = document.getElementById('patientModalOverlay');
    if (!modalOverlay) return;

    const closeBtn = document.getElementById('patientModalCloseBtn');

    function openModal(data) {
        const modalName = document.getElementById('modalPatientName');
        const modalNid = document.getElementById('modalNationalId');
        const modalPhone = document.getElementById('modalPhone');
        const modalPoints = document.getElementById('modalPoints');
        const modalUsedPoints = document.getElementById('modalUsedPoints');

        if (modalName) modalName.textContent = data.name || '—';
        if (modalNid) modalNid.textContent = data.nid || '—';
        if (modalPhone) modalPhone.textContent = data.phone || '—';
        
        const remainingPoints = parseInt(data.points) || 0;
        if (modalPoints) modalPoints.textContent = remainingPoints;
        if (modalUsedPoints) modalUsedPoints.textContent = 100 - remainingPoints;

        // Fetch patient details via AJAX
        if (data.id && wrapper) {
            const baseUrl = wrapper.dataset.patientBaseUrl;
            fetch(baseUrl + '/' + data.id + '?json=1')
                .then(r => r.json())
                .then(info => {
                    const mTotalPresc = document.getElementById('modalTotalPrescriptions');
                    const mTotalMeds = document.getElementById('modalTotalMeds');
                    const mTotalPoints = document.getElementById('modalTotalPoints');
                    const mLastDate = document.getElementById('modalLastDate');
                    const mLastCenter = document.getElementById('modalLastCenter');
                    const mLastDoctor = document.getElementById('modalLastDoctor');

                    if (mTotalPresc) mTotalPresc.textContent = info.total_prescriptions || '0';
                    if (mTotalMeds) mTotalMeds.textContent = info.total_meds || '0';
                    if (mTotalPoints) mTotalPoints.textContent = info.total_points || '0';
                    if (mLastDate) mLastDate.textContent = info.last_visit_date || '—';
                    if (mLastCenter) mLastCenter.textContent = info.last_visit_center || '—';
                    if (mLastDoctor) mLastDoctor.textContent = info.last_visit_doctor || '—';

                    // Fill history table
                    const tbody = document.getElementById('historyTableBody');
                    if (tbody) {
                        if (info.dispense_history && info.dispense_history.length > 0) {
                            tbody.innerHTML = info.dispense_history.map(h =>
                                `<tr>
                                    <td class="drug">
                                        <div>${h.medicine}</div>
                                        ${h.medicine_en ? `<div style="font-size:11px; color:#64748b; font-style:italic; margin-top:2px;">${h.medicine_en}</div>` : ''}
                                    </td>
                                    <td>${h.quantity}</td>
                                    <td>${h.points}</td>
                                    <td>${h.date}</td>
                                    <td class="pharm">${h.pharmacist}</td>
                                </tr>`
                            ).join('');
                        } else {
                            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">لا يوجد سجل صرف</td></tr>';
                        }
                    }
                })
                .catch(() => {});
        }

        modalOverlay.classList.add('is-open');
        modalOverlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modalOverlay.classList.remove('is-open');
        modalOverlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    if (closeBtn) closeBtn.addEventListener('click', closeModal);

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.patients-action-btn');
        if (!btn) return;
        const tr = btn.closest('.patients-tr');
        if (!tr) return;
        openModal({
            id: tr.dataset.patientId,
            name: tr.dataset.patientName,
            nid: tr.dataset.patientNid,
            phone: tr.dataset.patientPhone,
            points: tr.dataset.patientPoints
        });
    });

    modalOverlay.addEventListener('click', function (e) {
        if (e.target === modalOverlay) closeModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modalOverlay.classList.contains('is-open')) closeModal();
    });

    // Modal tabs
    const tabs = Array.from(modalOverlay.querySelectorAll('.patient-modal-tab'));
    const panes = Array.from(modalOverlay.querySelectorAll('.patient-modal-tabpane'));
    tabs.forEach(t => {
        t.addEventListener('click', () => {
            const tabName = t.getAttribute('data-tab');
            tabs.forEach(tb => {
                tb.classList.toggle('is-active', tb.getAttribute('data-tab') === tabName);
                tb.setAttribute('aria-selected', tb.getAttribute('data-tab') === tabName ? 'true' : 'false');
            });
            panes.forEach(p => p.classList.toggle('is-active', p.getAttribute('data-pane') === tabName));
        });
    });
})();
