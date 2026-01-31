function showPatientInfo(name, id, phone, address, dob, centers) {
    document.getElementById('v-name').value = name;
    document.getElementById('v-national-id').value = id;
    document.getElementById('v-phone').value = phone;
    document.getElementById('v-address').value = address || '---';
    document.getElementById('v-dob').value = dob || '---';
    document.getElementById('v-centers').value = centers || '---';
    document.getElementById('patientViewModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function hidePatientInfo() {
    document.getElementById('patientViewModal').style.display = 'none';
    document.body.style.overflow = '';
}

// Close on overlay click
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('patientViewModal');
    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === this) hidePatientInfo();
        });
    }
});
