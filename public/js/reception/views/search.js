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
