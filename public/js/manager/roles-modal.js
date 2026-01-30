
(function () {
  const overlay = document.getElementById("roleEditOverlay");
  if (!overlay) return;

  const nameInput = document.getElementById("roleNameInput");
  const usersInput = document.getElementById("roleUsersInput");
  const cancelBtn = document.getElementById("roleCancelBtn");
  const saveBtn = document.getElementById("roleSaveBtn");

  let activeRow = null;

  function openModal(row) {
    activeRow = row;

    const cells = row ? row.querySelectorAll("td") : [];
    const roleNameCell = cells.length ? cells[cells.length - 1] : null;
    const usersCountCell = cells.length >= 3 ? cells[2] : null;

    const roleName = roleNameCell ? roleNameCell.textContent.trim() : "";
    const usersCount = usersCountCell ? usersCountCell.textContent.trim() : "";

    nameInput.value = roleName;
    const normalized = usersCount.replace(/[٠-٩]/g, d => "٠١٢٣٤٥٦٧٨٩".indexOf(d)).replace(/[^\d]/g, "");
    usersInput.value = normalized || "";

    overlay.classList.add("is-open");
    overlay.setAttribute("aria-hidden", "false");
    document.body.classList.add("modal-open");

    setTimeout(() => nameInput && nameInput.focus(), 0);
  }

  function closeModal() {
    overlay.classList.remove("is-open");
    overlay.setAttribute("aria-hidden", "true");
    document.body.classList.remove("modal-open");
    activeRow = null;
  }

  function toArabicDigits(str) {
    const map = { "0": "٠", "1": "١", "2": "٢", "3": "٣", "4": "٤", "5": "٥", "6": "٦", "7": "٧", "8": "٨", "9": "٩" };
    return String(str).replace(/\d/g, (d) => map[d] || d);
  }

  document.addEventListener("click", function (e) {
    const btn = e.target.closest(".role-edit-trigger");
    if (!btn) return;

    const row = btn.closest("tr");
    if (!row) return;

    e.preventDefault();
    openModal(row);
  });

  overlay.addEventListener("click", function (e) {
    if (e.target === overlay) closeModal();
  });

  document.addEventListener("keydown", function (e) {
    if (!overlay.classList.contains("is-open")) return;
    if (e.key === "Escape") closeModal();
  });

  cancelBtn && cancelBtn.addEventListener("click", closeModal);

  saveBtn && saveBtn.addEventListener("click", function () {
    if (!activeRow) return;

    const newName = (nameInput.value || "").trim();
    const newUsers = (usersInput.value || "").trim();

    const cells = activeRow.querySelectorAll("td");
    const roleNameCell = cells.length ? cells[cells.length - 1] : null;
    const usersCountCell = cells.length >= 3 ? cells[2] : null;

    if (roleNameCell) roleNameCell.textContent = newName || roleNameCell.textContent;
    if (usersCountCell) usersCountCell.textContent = newUsers ? toArabicDigits(newUsers) : usersCountCell.textContent;

    closeModal();
  });
})();
