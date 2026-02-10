(function () {
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');

    function closeAll() {
        if (userDropdown) {
            userDropdown.classList.remove('open');
            userDropdown.setAttribute('aria-hidden', 'true');
        }
    }

    if (userMenuBtn) {
        userMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (userDropdown) {
                const open = userDropdown.classList.toggle('open');
                userDropdown.setAttribute('aria-hidden', open ? 'false' : 'true');
            }
        });
    }

    document.addEventListener('click', () => closeAll());
})();
