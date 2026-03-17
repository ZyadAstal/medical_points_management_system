document.getElementById('resetBtn')?.addEventListener('click', function () {
    const nameInput = document.getElementById('patient-name');
    const idInput = document.getElementById('patient-id');
    const phoneInput = document.getElementById('patient-phone');
    const resetUrl = document.body.dataset.searchResetUrl;

    if (nameInput) nameInput.value = '';
    if (idInput) idInput.value = '';
    if (phoneInput) phoneInput.value = '';
    
    if (resetUrl) {
        window.location.href = resetUrl;
    }
});
