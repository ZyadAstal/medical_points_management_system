function qs(sel, root = document) { return root.querySelector(sel); }
function qsa(sel, root = document) { return [...root.querySelectorAll(sel)]; }

function initRowMenus() {
  qsa("[data-rowmenu-btn]").forEach(btn => {
    btn.addEventListener("click", (e) => {
      e.stopPropagation();
      const id = btn.getAttribute("data-rowmenu-btn");
      const panel = qs(`[data-rowmenu-panel="${id}"]`);
      qsa(".rowmenu__panel").forEach(p => { if (p !== panel) p.classList.remove("open"); });
      if (panel) panel.classList.toggle("open");
    });
  });
}

function bootApp(){
  initRowMenus();
  initPasswordToggles();
  initChangePasswordValidation();
}

function initPasswordToggles(){
  const toggles = qsa('[data-toggle-target], [data-toggle-password]');
  if (!toggles.length) return;

  toggles.forEach(btn => {
    btn.addEventListener('click', () => {
      const targetId = btn.getAttribute('data-toggle-target') || btn.getAttribute('data-toggle-password');
      if (!targetId) return;
      const input = document.getElementById(targetId);
      if (!input) return;

      const isPassword = input.getAttribute('type') === 'password';
      input.setAttribute('type', isPassword ? 'text' : 'password');
      btn.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
    });
  });
}

function initChangePasswordValidation() {
  const form = document.getElementById('changePassForm');
  const newPass = document.getElementById('newPassword');
  const confirmPass = document.getElementById('confirmPassword');
  if (!form || !newPass || !confirmPass) return;

  function validate() {
    newPass.setCustomValidity('');
    confirmPass.setCustomValidity('');

    if (newPass.value && newPass.value.length < 8) {
      newPass.setCustomValidity('كلمة المرور يجب أن تكون 8 أحرف/أرقام على الأقل');
    }
    if (confirmPass.value && confirmPass.value.length < 8) {
      confirmPass.setCustomValidity('تأكيد كلمة المرور يجب أن يكون 8 أحرف/أرقام على الأقل');
    }

    if (newPass.value && confirmPass.value && newPass.value !== confirmPass.value) {
      confirmPass.setCustomValidity('كلمتا المرور غير متطابقتين');
    }
  }

  newPass.addEventListener('input', validate);
  confirmPass.addEventListener('input', validate);

  form.addEventListener('submit', function (e) {
    validate();
    if (!form.checkValidity()) {
      e.preventDefault();
      form.reportValidity();
    }
  });
}

document.addEventListener("DOMContentLoaded", bootApp);


function initMedicinesEditModal(){
  const body = document.body;
  if(!body || body.getAttribute("data-page") !== "medicines") return;

  const overlay = document.getElementById("editDrugModal");
  if(!overlay) return;

  const openModal = () => {
    overlay.classList.add("is-open");
    overlay.setAttribute("aria-hidden","false");
  };

  const closeModal = () => {
    overlay.classList.remove("is-open");
    overlay.setAttribute("aria-hidden","true");
  };


  document.addEventListener("click", (e) => {
    const target = e.target;
    if(!target) return;

    const editBtn = target.closest ? target.closest(".action-edit") : null;
    if(editBtn){
      e.preventDefault();
      fillEditDrugModalFromRow(editBtn);
      openModal();
      return;
    }

    if(target === overlay){
      closeModal();
      return;
    }

    const updateBtn = target.closest ? target.closest("#editDrugUpdate") : null;
    if(updateBtn){
      e.preventDefault();
      const row = window.__currentMedicineRow;
      if(row){
        const nameInput = document.getElementById("editDrugName");
        const pointsInput = document.getElementById("editDrugPoints");
        const expiryInput = document.getElementById("editDrugExpiry");

        const nameEl = row.querySelector(".col-drug-cell");
        const pointsEl = row.querySelector(".col-points-cell");
        const expiryEl = row.querySelector(".col-expiry-cell");

        if(nameEl && nameInput) nameEl.textContent = nameInput.value.trim();
        if(pointsEl && pointsInput) pointsEl.textContent = pointsInput.value.trim();
        if(expiryEl && expiryInput) expiryEl.textContent = expiryInput.value.trim();
      }
      closeModal();
      return;
    }

    const cancelBtn = target.closest ? target.closest("#editDrugCancel") : null;
    if(cancelBtn){
      e.preventDefault();
      closeModal();
      return;
    }
  });

  document.addEventListener("keydown", (e) => {
    if(e.key === "Escape" && overlay.classList.contains("is-open")){
      closeModal();
    }
  });
}


if(document.readyState === "loading"){
  document.addEventListener("DOMContentLoaded", initMedicinesEditModal);
}else{
  initMedicinesEditModal();
}


function fillEditDrugModalFromRow(editBtn){
  const row = editBtn.closest("tr");
  if(!row) return;

  window.__currentMedicineRow = row;

  const nameEl = row.querySelector(".col-drug-cell");
  const pointsEl = row.querySelector(".col-points-cell");
  const expiryEl = row.querySelector(".col-expiry-cell");

  const nameInput = document.getElementById("editDrugName");
  const pointsInput = document.getElementById("editDrugPoints");
  const expiryInput = document.getElementById("editDrugExpiry");

  if(nameInput && nameEl) nameInput.value = nameEl.textContent.trim();
  if(pointsInput && pointsEl) pointsInput.value = pointsEl.textContent.trim();
  if(expiryInput && expiryEl) expiryInput.value = expiryEl.textContent.trim();
}


function initMedicinesAddModal(){
  const body = document.body;
  if(!body || body.getAttribute("data-page") !== "medicines") return;

  const overlay = document.getElementById("addDrugModal");
  const openBtn = document.getElementById("openAddDrugModal");
  if(!overlay || !openBtn) return;

  const openModal = () => {
    overlay.classList.add("is-open");
    overlay.setAttribute("aria-hidden","false");

    const nameInput = document.getElementById("addDrugName");
    const pointsInput = document.getElementById("addDrugPoints");
    const expiryInput = document.getElementById("addDrugExpiry");

    if(nameInput) nameInput.value = "";
    if(pointsInput) pointsInput.value = "";
    if(expiryInput) expiryInput.value = "";
  };

  const closeModal = () => {
    overlay.classList.remove("is-open");
    overlay.setAttribute("aria-hidden","true");
  };

  openBtn.addEventListener("click", (e) => {
    e.preventDefault();
    openModal();
  });

  overlay.addEventListener("click", (e) => {
    if(e.target === overlay){
      closeModal();
    }
  });

  document.addEventListener("keydown", (e) => {
    if(e.key === "Escape" && overlay.classList.contains("is-open")){
      closeModal();
    }
  });

  document.addEventListener("click", (e) => {
    const target = e.target;

    const cancelBtn = target && target.closest ? target.closest("#addDrugCancel") : null;
    if(cancelBtn){
      e.preventDefault();
      closeModal();
      return;
    }

    const saveBtn = target && target.closest ? target.closest("#addDrugSave") : null;
    if(saveBtn){
      e.preventDefault();

      const nameInput = document.getElementById("addDrugName");
      const pointsInput = document.getElementById("addDrugPoints");
      const expiryInput = document.getElementById("addDrugExpiry");

      const name = nameInput ? nameInput.value.trim() : "";
      const points = pointsInput ? pointsInput.value.trim() : "";
      const expiry = expiryInput ? expiryInput.value.trim() : "";

      if(!name || !points || !expiry){
        closeModal();
        return;
      }

      const tbody = document.querySelector(".medicines-table tbody");
      if(tbody){
        const tr = document.createElement("tr");

        tr.innerHTML = `
          <td class="col-drug-cell"></td>
          <td class="col-points-cell"></td>
          <td class="col-expiry-cell"></td>
          <td class="col-actions-cell">
            <div class="action-edit" role="button" tabindex="0" aria-label="تعديل الدواء">
              <img src="../assets/icons/edit.svg" alt="edit" />
            </div>
          </td>
        `;

        const nameEl = tr.querySelector(".col-drug-cell");
        const pointsEl = tr.querySelector(".col-points-cell");
        const expiryEl = tr.querySelector(".col-expiry-cell");

        if(nameEl) nameEl.textContent = name;
        if(pointsEl) pointsEl.textContent = points;
        if(expiryEl) expiryEl.textContent = expiry;

        tbody.appendChild(tr);
      }

      closeModal();
      return;
    }
  });
}

if(document.readyState === "loading"){
  document.addEventListener("DOMContentLoaded", initMedicinesAddModal);
}else{
  initMedicinesAddModal();
}


function initMedicinesPagination(){
  const body = document.body;
  if(!body || body.getAttribute("data-page") !== "medicines") return;

  const tableBody = document.querySelector(".medicines-table tbody");
  const pagesWrap = document.getElementById("pgPages");
  const prevBtn = document.getElementById("pgPrev");
  const nextBtn = document.getElementById("pgNext");
  if(!tableBody || !pagesWrap || !prevBtn || !nextBtn) return;

  const perPage = 5;

  const positionMedicinesPagination = () => {
    const tableWrap = document.querySelector(".medicines-table-wrap");
    const pag = document.querySelector(".medicines-pagination");
    if(!tableWrap || !pag) return;


    const top = tableWrap.offsetTop + tableWrap.offsetHeight + 10;
    pag.style.top = top + "px";
  };

  const setActive = (pageNum) => {
    const pageButtons = pagesWrap.querySelectorAll(".pg-page");
    pageButtons.forEach(btn => btn.classList.remove("is-active"));
    const activeBtn = pagesWrap.querySelector('.pg-page[data-page="' + pageNum + '"]');
    if(activeBtn) activeBtn.classList.add("is-active");

    const rows = Array.from(tableBody.querySelectorAll("tr"));
    rows.forEach((row, idx) => {
      const start = (pageNum - 1) * perPage;
      const end = start + perPage;
      row.style.display = (idx >= start && idx < end) ? "" : "none";
    });

    body.dataset.medsPage = String(pageNum);
  };


  setActive(1);
  positionMedicinesPagination();

  pagesWrap.addEventListener("click", (e) => {
    const btn = e.target.closest ? e.target.closest(".pg-page") : null;
    if(!btn) return;
    const pageNum = parseInt(btn.getAttribute("data-page"), 10);
    if(!Number.isFinite(pageNum)) return;
    setActive(pageNum);
  });

  prevBtn.addEventListener("click", () => {
    const current = parseInt(body.dataset.medsPage || "1", 10);
    const next = Math.min(3, current + 1);
    setActive(next);
  });

  nextBtn.addEventListener("click", () => {
    const current = parseInt(body.dataset.medsPage || "1", 10);
    const next = Math.max(1, current - 1);
    setActive(next);
  });


  const observer = new MutationObserver(() => {
    const current = parseInt(body.dataset.medsPage || "1", 10);
    setActive(current);
    positionMedicinesPagination();
  });
  observer.observe(tableBody, { childList: true });
  window.addEventListener('resize', positionMedicinesPagination);
}

if(document.readyState === "loading"){
  document.addEventListener("DOMContentLoaded", initMedicinesPagination);
}else{
  initMedicinesPagination();
}
