document.getElementById('resetBtn')?.addEventListener('click', function () {
    const nameInput = document.getElementById('patient-name');
    const idInput = document.getElementById('patient-id');
    const phoneInput = document.getElementById('patient-phone');
    const wrapper = document.querySelector('.search-patient-wrapper');
    const resetUrl = wrapper ? wrapper.dataset.searchResetUrl : window.location.pathname;

    if (nameInput) nameInput.value = '';
    if (idInput) idInput.value = '';
    if (phoneInput) phoneInput.value = '';
    
    if (resetUrl) {
        window.location.href = resetUrl;
    }
});

window.openEditPatientModal = function(id, fullName, nationalId, phone, address, points) {
    document.getElementById('editPatientId').value = id;
    document.getElementById('editFullName').value = fullName;
    document.getElementById('editNationalId').value = nationalId;
    document.getElementById('editPhone').value = phone || '';
    document.getElementById('editAddress').value = address || '';
    document.getElementById('editPoints').value = points || 0;

    const form = document.getElementById('editPatientForm');
    if (form) {
        form.action = '/reception/patients/' + id;
    }

    const overlay = document.getElementById('editPatientOverlay');
    if (overlay) overlay.style.display = 'flex';
};

window.closeEditPatientModal = function() {
    const overlay = document.getElementById('editPatientOverlay');
    if (overlay) overlay.style.display = 'none';
};

// Close modal when clicking outside of it
document.addEventListener('click', function(e) {
    const overlay = document.getElementById('editPatientOverlay');
    if (overlay && e.target === overlay) {
        window.closeEditPatientModal();
    }
});
