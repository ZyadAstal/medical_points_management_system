(function() {
    let currentPatient = null;
    let selectedMedicines = []; // Array of {id, name, points, quantity}

    window.goStep = function(step) {
        for(let i=1; i<=4; i++) {
            const stepEl = document.getElementById('step'+i);
            const icon = document.getElementById('step'+i+'icon');
            if (stepEl) stepEl.classList.add('hidden');
            if (icon) {
                icon.classList.remove('active');
                icon.innerHTML = i;
            }
        }

        const currentStepEl = document.getElementById('step'+step);
        if (currentStepEl) currentStepEl.classList.remove('hidden');

        for(let i=1; i<step; i++) {
            const icon = document.getElementById('step'+i+'icon');
            if (icon) {
                icon.classList.add('active');
                icon.innerHTML = "✔";
            }
        }

        const currentIcon = document.getElementById('step'+step+'icon');
        if (currentIcon) currentIcon.classList.add('active');
    };

    window.searchPatient = async function() {
        const input = document.getElementById('national_id_input');
        if (!input) return;
        
        const nid = input.value;
        const errorDiv = document.getElementById('search_error');
        const wrapper = document.querySelector('.pharmacist-prescription-create-wrapper');
        const url = wrapper ? wrapper.dataset.searchUrl : null;
        
        if (errorDiv) errorDiv.style.display = 'none';

        if(!nid) {
            if (errorDiv) {
                errorDiv.innerText = "يرجى إدخال رقم الهوية";
                errorDiv.style.display = 'block';
            }
            return;
        }

        try {
            const response = await fetch(`${url}?national_id=${nid}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();

            if(data.success) {
                currentPatient = data.patient;
                
                const nameEl = document.getElementById('display_patient_name');
                const pointsEl = document.getElementById('display_patient_points');
                const rNameEl = document.getElementById('review_patient_name');
                const rIdEl = document.getElementById('review_patient_id');
                const formIdEl = document.getElementById('form_patient_id');

                if (nameEl) nameEl.innerText = currentPatient.name || currentPatient.full_name || "غير معروف";
                if (pointsEl) pointsEl.innerText = (currentPatient.points || 0) + " نقطة";
                if (rNameEl) rNameEl.innerText = currentPatient.name || currentPatient.full_name || "غير معروف";
                if (rIdEl) rIdEl.innerText = currentPatient.national_id || nid;
                if (formIdEl) formIdEl.value = currentPatient.id;

                window.goStep(2);
            } else {
                if (errorDiv) {
                    errorDiv.innerText = data.message;
                    errorDiv.style.display = 'block';
                }
            }
        } catch (e) {
            console.error(e);
            if (errorDiv) {
                errorDiv.innerText = "حدث خطأ أثناء البحث";
                errorDiv.style.display = 'block';
            }
        }
    };

    window.addDrugToList = function() {
        const select = document.getElementById('drugSelect');
        const qtyInput = document.getElementById('drugQuantity');
        
        if(!select || !select.value) return;
        
        const id = select.value;
        const name = select.options[select.selectedIndex].getAttribute('data-name');
        const points = parseInt(select.options[select.selectedIndex].getAttribute('data-points'));
        const quantity = parseInt(qtyInput.value);

        if(isNaN(quantity) || quantity < 0) {
            alert("يرجى إدخال كمية صالحة");
            return;
        }

        // Check if duplicate
        const existing = selectedMedicines.find(m => m.id === id);
        if(existing) {
            existing.quantity += quantity;
        } else {
            selectedMedicines.push({ id, name, points, quantity });
        }

        renderMedicinesTable();
        updateWizardTotal();
        
        // Reset inputs
        select.value = "";
        qtyInput.value = 1;
    };

    function renderMedicinesTable() {
        const container = document.getElementById('drug_rows_container');
        if (!container) return;
        
        container.innerHTML = selectedMedicines.map((m, index) => `
            <div class="table-row" style="background: #fff; padding: 10px; border-radius: 8px; margin-bottom: 8px;">
                <div style="flex: 2; text-align: right; font-weight: 600;">${m.name}</div>
                <div style="flex: 1;"><div class="value-box">${m.quantity}</div></div>
                <div style="flex: 1;"><div class="value-box" style="color: #0764AE;">${m.points * m.quantity}</div></div>
                <div style="width: 30px;">
                    <span class="delete-row" onclick="removeMedicine(${index})">🗑</span>
                </div>
            </div>
        `).join('');
    }

    window.removeMedicine = function(index) {
        selectedMedicines.splice(index, 1);
        renderMedicinesTable();
        updateWizardTotal();
    };

    function updateWizardTotal() {
        const totalEl = document.getElementById('wizard_total_cost');
        if (!totalEl) return;
        const total = selectedMedicines.reduce((sum, m) => sum + (m.points * m.quantity), 0);
        totalEl.innerText = total;
    }

    window.prepareStep4 = function() {
        if(selectedMedicines.length === 0) {
            alert("يرجى إضافة دواء واحد على الأقل.");
            return;
        }

        // Capture metadata
        const doctorSelect = document.getElementById('doctor_id');
        const pDateInput = document.getElementById('prescription_date');
        const pNotesInput = document.getElementById('prescription_notes');

        if(!doctorSelect || !doctorSelect.value) {
            alert("يرجى اختيار الطبيب");
            return;
        }

        const formDoctorIdEl = document.getElementById('form_doctor_id');
        const formPDateEl = document.getElementById('form_prescription_date');
        const formNotesEl = document.getElementById('form_notes');

        if (formDoctorIdEl) formDoctorIdEl.value = doctorSelect.value;
        if (formPDateEl && pDateInput) formPDateEl.value = pDateInput.value;
        if (formNotesEl && pNotesInput) formNotesEl.value = pNotesInput.value;

        const listContainer = document.getElementById('review_items_list');
        const hiddenContainer = document.getElementById('selected_items_hidden_container');
        
        if (listContainer) listContainer.innerHTML = '';
        if (hiddenContainer) hiddenContainer.innerHTML = '';

        let totalPoints = 0;

        selectedMedicines.forEach(m => {
            const cost = m.points * m.quantity;
            totalPoints += cost;

            // Review UI
            if (listContainer) {
                const div = document.createElement('div');
                div.className = 'review-item';
                div.innerHTML = `<span>${m.name}</span> <span style="color: #0B6CB8;">الكمية: ${m.quantity} (${cost} نقطة)</span>`;
                listContainer.appendChild(div);
            }

            // Hidden Inputs for Laravel array format
            if (hiddenContainer) {
                hiddenContainer.innerHTML += `
                    <input type="hidden" name="items[${m.id}][medicine_id]" value="${m.id}">
                    <input type="hidden" name="items[${m.id}][quantity]" value="${m.quantity}">
                `;
            }
        });

        const finalBalanceEl = document.getElementById('review_final_balance');
        if (finalBalanceEl && currentPatient) {
            const finalBalance = currentPatient.points - totalPoints;
            finalBalanceEl.innerText = finalBalance;
            
            if(finalBalance < 0) {
                finalBalanceEl.parentElement.style.color = 'red';
            } else {
                finalBalanceEl.parentElement.style.color = '#059669';
            }
        }

        window.goStep(4);
    };

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        window.goStep(1);
    });
})();
