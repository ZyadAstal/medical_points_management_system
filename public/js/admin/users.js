(function () {
  const table = document.querySelector('.users-table');
  if (!table) return;

  function closeAllMenus() {
    document.querySelectorAll('.filter-menu.open').forEach(m => {
      m.classList.remove('open');
      m.setAttribute('aria-hidden', 'true');
    });
  }

  function setupFilter(filterSelector, menuSelector, hiddenInputId) {
    const filter = document.querySelector(filterSelector);
    if (!filter) return;

    const box = filter.querySelector('.filter-box');
    const textEl = filter.querySelector('.filter-text');
    const menu = filter.querySelector(menuSelector);
    const hiddenInput = document.getElementById(hiddenInputId);
    const form = document.querySelector('.users-filters-form');

    if (box) {
      box.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const isOpen = menu && menu.classList.contains('open');
        closeAllMenus();
        if (menu && !isOpen) {
          menu.classList.add('open');
          menu.setAttribute('aria-hidden', 'false');
        }
      });
    }

    if (menu) {
      menu.addEventListener('click', function (e) {
        const item = e.target.closest('.menu-item');
        if (!item) return;

        const value = item.getAttribute('data-value');
        const label = item.textContent.trim();

        if (textEl) textEl.textContent = label;
        if (hiddenInput) hiddenInput.value = value;

        closeAllMenus();
        if (form) form.submit();
      });
    }
  }

  setupFilter('.users-filter.filter-role', '.role-menu', 'hidden_filter_role');
  setupFilter('.users-filter.filter-center', '.center-menu', 'hidden_filter_center');

  // Modals Logic
  const addOverlay = document.getElementById('userAddOverlay');
  const editOverlay = document.getElementById('userEditOverlay');
  const changePassOverlay = document.getElementById('userChangePassOverlay');

  const addBtn = document.getElementById('mcAddUserBtn') || document.querySelector('.users-add-btn');
  const editCancelBtn = document.getElementById('userEditCancelBtn');
  const addCancelBtn = document.getElementById('userAddCancelBtn');
  const changePassCancelBtn = document.getElementById('changePassCancelBtn');

  function openOverlay(overlay) {
    if (!overlay) return;
    overlay.classList.add('is-open');
    overlay.setAttribute('aria-hidden', 'false');
    document.body.classList.add('modal-open');
  }

  function closeOverlay(overlay) {
    if (!overlay) return;
    overlay.classList.remove('is-open');
    overlay.setAttribute('aria-hidden', 'true');
    const anyOpen = document.querySelector('.modal-overlay.is-open');
    if (!anyOpen) document.body.classList.remove('modal-open');
  }

  if (addBtn) addBtn.onclick = () => openOverlay(addOverlay);
  if (addCancelBtn) addCancelBtn.onclick = () => closeOverlay(addOverlay);
  if (editCancelBtn) editCancelBtn.onclick = () => closeOverlay(editOverlay);
  if (changePassCancelBtn) changePassCancelBtn.onclick = () => closeOverlay(changePassOverlay);

  // Global close
  document.addEventListener('click', () => closeAllMenus());

  window.editUser = function (id, name, username, email, role_id, center_id) {
    const overlay = document.getElementById('userEditOverlay');
    const form = document.getElementById('userEditForm');
    if (!overlay || !form) return;

    // Update form action
    form.action = `/superadmin/users/${id}`;

    // Populate fields
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-username').value = username;
    document.getElementById('edit-email').value = email;
    document.getElementById('edit-role').value = role_id;
    document.getElementById('edit-center').value = center_id || '';

    openOverlay(overlay);
  };
})();
