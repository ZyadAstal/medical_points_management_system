(function () {
    const notifBtn = document.getElementById('notifBtn');
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');
    const notifMenu = document.getElementById('notifMenu');
    const notifDot = document.getElementById('notifDot');

    function closeAll() {
        if (notifMenu) {
            notifMenu.classList.remove('open');
            notifMenu.setAttribute('aria-hidden', 'true');
        }
        if (userDropdown) {
            userDropdown.classList.remove('open');
            userDropdown.setAttribute('aria-hidden', 'true');
        }
    }

    if (userMenuBtn) {
        userMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (notifMenu) {
                notifMenu.classList.remove('open');
                notifMenu.setAttribute('aria-hidden', 'true');
            }
            if (userDropdown) {
                const open = userDropdown.classList.toggle('open');
                userDropdown.setAttribute('aria-hidden', open ? 'false' : 'true');
            }
        });
    }

    if (notifBtn) {
        notifBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (userDropdown) {
                userDropdown.classList.remove('open');
                userDropdown.setAttribute('aria-hidden', 'true');
            }
            if (notifMenu) {
                const open = notifMenu.classList.toggle('open');
                notifMenu.setAttribute('aria-hidden', open ? 'false' : 'true');
            }
        });
    }

    document.addEventListener('click', () => closeAll());
})();
