const modal = document.getElementById('modalOverlay');

function openPresModal(data) {
    document.getElementById('modalDoc').innerText = 'د. ' + data.doctor.name;
    document.getElementById('modalCenter').innerText = data.medical_center ? data.medical_center.name : '---';
    document.getElementById('modalDate').innerText = new Date(data.created_at).toLocaleDateString('ar-EG');
    document.getElementById('modalNote').innerText = data.notes || 'لا توجد ملاحظات';

    const tbody = document.getElementById('modalTableBody');
    tbody.innerHTML = "";
    
    data.items.forEach(item => {
        tbody.innerHTML += `
            <tr>
                <td>${item.medicine.name}</td>
                <td>${item.quantity}</td>
                <td>${item.is_dispensed ? item.quantity : 0}</td>
            </tr>
        `;
    });

    modal.style.display = 'flex';
}

function closeModal() {
    modal.style.display = 'none';
}

if (modal) {
    window.onclick = function(e) { if (e.target == modal) closeModal(); }
}
