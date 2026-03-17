(function () {
    const moreBtn = document.getElementById('showMoreBtn');
    const lessBtn = document.getElementById('showLessBtn');
    if (!moreBtn || !lessBtn) return;

    const extras = document.querySelectorAll('.row-extra');

    moreBtn.addEventListener('click', function () {
        extras.forEach(r => r.classList.remove('hidden-row'));
        moreBtn.style.display = 'none';
        lessBtn.style.display = '';
    });

    lessBtn.addEventListener('click', function () {
        extras.forEach(r => r.classList.add('hidden-row'));
        lessBtn.style.display = 'none';
        moreBtn.style.display = '';
    });
})();
