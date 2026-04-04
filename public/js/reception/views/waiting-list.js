function printWaitingList() {
    const dateInput = document.getElementById('waitingDate');
    if (!dateInput) return;
    const date = dateInput.value;
    const wrapper = document.querySelector('.reception-waiting-list-content-wrapper');
    const baseUrl = wrapper ? wrapper.dataset.waitingListUrl : '';
    const url = `${baseUrl}?date=${date}&print=1`;
    window.open(url, '_blank');
}

(function () {
    const confirmModal = document.getElementById('confirmModal');
    const confirmText = document.getElementById('confirmModalText');
    const confirmYes = document.getElementById('confirmBtnYes');
    const confirmNo = document.getElementById('confirmBtnNo');

    function showCustomConfirm(msg) {
        return new Promise((resolve) => {
            confirmText.textContent = msg;
            confirmModal.classList.add('is-visible');

            const handleYes = () => {
                cleanup();
                resolve(true);
            };
            const handleNo = () => {
                cleanup();
                resolve(false);
            };
            const cleanup = () => {
                confirmYes.removeEventListener('click', handleYes);
                confirmNo.removeEventListener('click', handleNo);
                confirmModal.classList.remove('is-visible');
            };

            confirmYes.addEventListener('click', handleYes);
            confirmNo.addEventListener('click', handleNo);
        });
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function initButtons() {
        // ---- زر الإدخال للعيادة ----
        document.querySelectorAll('.js-enter-btn:not(.initialized)').forEach(btn => {
            btn.classList.add('initialized');
            btn.addEventListener('click', function () {
                if (this.disabled) return;
                const url = this.dataset.url;
                const row = this.closest('.waiting-row');
                const badge = row.querySelector('.js-status-badge');

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        badge.textContent = 'بانتظار';
                        badge.className = 'waiting-status-badge waiting-status-waiting js-status-badge';
                        this.disabled = true;
                        this.style.backgroundColor = '#F3F3F3';
                        this.style.color = '#A0A0A0';
                        this.style.cursor = 'not-allowed';
                        this.textContent = 'تم الإدخال';
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('حدث خطأ أثناء الاتصال بالسيرفر');
                });
            });
        });

        // ---- زر تراجع (إلغاء أو فك إدخال) ----
        document.querySelectorAll('.js-undo-btn:not(.initialized)').forEach(btn => {
            btn.classList.add('initialized');
            btn.addEventListener('click', async function () {
                if (this.disabled) return;
                const row = this.closest('.waiting-row');
                const badge = row.querySelector('.js-status-badge');
                const isWaiting = badge.textContent.trim() === 'بانتظار';
                
                const msg = isWaiting ? 'هل تريد التراجع عن إدخال المريض؟' : 'هل تريد إلغاء موعد هذا المريض نهائياً؟';
                if (!(await showCustomConfirm(msg))) return;

                const url = this.dataset.url;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        badge.textContent = data.status_label;
                        badge.className = 'waiting-status-badge ' + data.status_class + ' js-status-badge';
                        
                        if (data.new_status === 'registered') {
                            const enterBtn = row.querySelector('.js-enter-btn');
                            if (enterBtn) {
                                enterBtn.disabled = false;
                                enterBtn.style.backgroundColor = '#E6F3FF';
                                enterBtn.style.color = '#0B6CB8';
                                enterBtn.style.cursor = 'pointer';
                                enterBtn.textContent = 'إدخال للعيادة';
                            }
                        } else if (data.new_status === 'cancelled') {
                            row.querySelectorAll('.waiting-text-action-btn').forEach(b => {
                                b.disabled = true;
                                b.style.backgroundColor = '#F3F3F3';
                                b.style.color = '#A0A0A0';
                                b.style.cursor = 'not-allowed';
                            });
                            this.textContent = 'تم الإلغاء';
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('حدث خطأ أثناء الاتصال بالسيرفر');
                });
            });
        });
    }

    initButtons();
})();
