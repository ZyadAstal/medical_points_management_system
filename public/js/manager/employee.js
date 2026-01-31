function toggleMenu(menuId) {
    const menu = document.getElementById(menuId);
    if (!menu) return;

    const isOpen = menu.classList.contains('open');
    closeAllMenus();
    if (!isOpen) {
        menu.classList.add('open');
        menu.setAttribute('aria-hidden', 'false');
    }
}

function closeAllMenus() {
    document.querySelectorAll('.filter-menu.open').forEach(m => {
        m.classList.remove('open');
        m.setAttribute('aria-hidden', 'true');
    });
}

function selectRole(value, label) {
    const roleInput = document.getElementById('roleInput');
    const filterText = document.querySelector('.filter-role .filter-text');

    if (roleInput && filterText) {
        roleInput.value = value;
        filterText.textContent = label;
        document.getElementById('filterForm').submit();
    }
}

function openAddModal() {
    const overlay = document.getElementById('userAddOverlay');
    if (overlay) {
        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
    }
}

function openEditModal(id, name, username, email, roleId) {
    const overlay = document.getElementById('userEditOverlay');
    const form = document.getElementById('userEditForm');

    if (overlay && form) {
        form.action = `/manager/staff/${id}`;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-username').value = username;
        document.getElementById('edit-email').value = email;
        document.getElementById('edit-role').value = roleId;

        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Close menus on click outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.users-filter')) {
            closeAllMenus();
        }
    });

    const cancelBtns = document.querySelectorAll('#userAddCancelBtn, #userEditCancelBtn');
    const overlays = document.querySelectorAll('.modal-overlay');

    function closeAllOverlays() {
        overlays.forEach(ov => {
            ov.classList.remove('is-open');
            ov.setAttribute('aria-hidden', 'true');
        });
        document.body.classList.remove('modal-open');
    }

    cancelBtns.forEach(btn => {
        btn.addEventListener('click', closeAllOverlays);
    });

    overlays.forEach(ov => {
        ov.addEventListener('click', function (e) {
            if (e.target === ov) closeAllOverlays();
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAllMenus();
            closeAllOverlays();
        }
    });
    // Debounce search input
    const searchInput = document.getElementById('staffSearchInput');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 800);
        });
    }
});
