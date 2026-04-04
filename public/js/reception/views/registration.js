(function () {
    // ---- Tabs ----
    const tabs = Array.from(document.querySelectorAll('.pr-tab'));
    const panels = Array.from(document.querySelectorAll('.pr-panel'));

    function activate(targetId) {
        tabs.forEach(tab => {
            const active = tab.dataset.target === targetId;
            tab.classList.toggle('is-active', active);
            tab.setAttribute('aria-selected', active ? 'true' : 'false');
        });
        panels.forEach(panel => {
            const active = panel.id === targetId;
            panel.classList.toggle('is-active', active);
            panel.hidden = !active;
        });
    }
    tabs.forEach(tab => tab.addEventListener('click', () => activate(tab.dataset.target)));

    // Handle initial panel if errors
    const wrapper = document.querySelector('.patient-registration-wrapper');
    if (wrapper && wrapper.dataset.initialPanel) {
        activate(wrapper.dataset.initialPanel);
    }

    // ---- بحث مريض سابق ----
    const searchBtn = document.getElementById('searchExistingBtn');
    const resultBox = document.getElementById('existingPatientResult');
    const searchMsg = document.getElementById('searchMsg');
    const cancelBtn = document.getElementById('cancelExistingBtn');
    const saveBtn = document.getElementById('saveExistingBtn');
    const sendDoctorBtn = document.getElementById('sendToDoctorBtn');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (searchBtn) {
        searchBtn.addEventListener('click', function () {
            const nationalId = document.getElementById('existingNationalId').value.trim();
            const searchUrl = wrapper.dataset.searchUrl;
            if (!nationalId) { showMsg('يرجى إدخال رقم الهوية'); return; }

            fetch(`${searchUrl}?national_id=${nationalId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (!data.found) {
                    showMsg('لم يتم العثور على مريض بهذا الرقم');
                    resultBox.style.display = 'none';
                    return;
                }
                hideMsg();
                document.getElementById('existingPatientId').value = data.id;
                document.getElementById('existingName').textContent = data.name;
                document.getElementById('existingIdDisplay').textContent = 'رقم الهوية : ' + data.national_id;
                document.getElementById('existingAddress').value = data.address ?? '';
                document.getElementById('existingPhone').value = data.phone ?? '';
                resultBox.style.display = 'block';
            })
            .catch(() => showMsg('حدث خطأ أثناء البحث'));
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function () {
            resultBox.style.display = 'none';
            document.getElementById('existingNationalId').value = '';
            hideMsg();
        });
    }

    // حفظ وتحديث بيانات مريض سابق
    if (saveBtn) {
        saveBtn.addEventListener('click', function () {
            const patientId = document.getElementById('existingPatientId').value;
            const address = document.getElementById('existingAddress').value;
            const phone = document.getElementById('existingPhone').value;

            fetch(`/reception/patients/${patientId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-HTTP-Method-Override': 'PUT',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ phone, address, _method: 'PUT' })
            })
            .then(r => r.json())
            .catch(() => {})
            .finally(() => showSuccessMsg('تم تحديث البيانات بنجاح'));
        });
    }

    // إرسال مريض سابق للطبيب
    if (sendDoctorBtn) {
        sendDoctorBtn.addEventListener('click', function () {
            const patientId = document.getElementById('existingPatientId').value;
            const doctorId = document.getElementById('existingDoctor').value;
            const priority = document.getElementById('existingPriority').value;

            if (!doctorId) { showMsg('يرجى اختيار الطبيب أولاً'); return; }
            if (!patientId) { showMsg('يرجى البحث عن المريض أولاً'); return; }

            fetch(`/reception/patients/${patientId}/send-to-doctor`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ doctor_id: doctorId, priority: priority })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showSuccessMsg(data.message ?? 'تم إضافة المريض لقائمة الانتظار');
                } else {
                    showMsg(data.message ?? 'حدث خطأ');
                }
            })
            .catch(() => showMsg('حدث خطأ أثناء الإرسال'));
        });
    }

    function showMsg(msg) {
        if (!searchMsg) return;
        searchMsg.textContent = msg;
        searchMsg.style.display = 'block';
        searchMsg.style.padding = '15px';
        searchMsg.style.borderRadius = '8px';
        searchMsg.style.marginBottom = '20px';
        searchMsg.style.border = '1px solid #f5c6cb';
        searchMsg.style.background = '#f8d7da';
        searchMsg.style.color = '#721c24';
        searchMsg.style.fontWeight = 'bold';
    }

    function showSuccessMsg(msg) {
        if (!searchMsg) return;
        searchMsg.textContent = msg;
        searchMsg.style.display = 'block';
        searchMsg.style.padding = '15px';
        searchMsg.style.borderRadius = '8px';
        searchMsg.style.marginBottom = '20px';
        searchMsg.style.border = '1px solid #c3e6cb';
        searchMsg.style.background = '#d4edda';
        searchMsg.style.color = '#155724';
        searchMsg.style.fontWeight = 'bold';
    }

    function hideMsg() {
        if (!searchMsg) return;
        searchMsg.style.display = 'none';
    }
})();
