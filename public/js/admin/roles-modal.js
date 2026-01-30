(function () {
  const overlay = document.getElementById("roleEditOverlay");
  const form = document.getElementById("roleEditForm");
  if (!overlay || !form) return;

  const nameInput = document.getElementById("roleNameInput");
  const usersInput = document.getElementById("roleUsersInput");
  const cancelBtn = document.getElementById("roleCancelBtn");

  window.editRole = function (id, name, users_count) {
    // Populate form
    form.action = `/superadmin/roles/${id}`;
    nameInput.value = name;
    usersInput.value = users_count;

    // Show modal
    overlay.classList.add("is-open");
    overlay.setAttribute("aria-hidden", "false");
    document.body.classList.add("modal-open");

    setTimeout(() => nameInput && nameInput.focus(), 50);
  };

  function closeModal() {
    overlay.classList.remove("is-open");
    overlay.setAttribute("aria-hidden", "true");
    document.body.classList.remove("modal-open");
  }

  overlay.addEventListener("click", function (e) {
    if (e.target === overlay) closeModal();
  });

  document.addEventListener("keydown", function (e) {
    if (!overlay.classList.contains("is-open")) return;
    if (e.key === "Escape") closeModal();
  });

  if (cancelBtn) cancelBtn.addEventListener("click", closeModal);
})();
