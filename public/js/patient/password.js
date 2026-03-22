document.addEventListener("DOMContentLoaded", function () {

    const modal = document.getElementById("passwordModal");
    if (!modal) {
        console.error("modal id غير موجود");
        return;
    }

    const changePasswordBtn = document.getElementById("changePasswordBtn");
    const cancelBtn = document.getElementById("cancelBtn");

    function openModal() {
        modal.style.display = "flex";
    }

    function closeModal() {
        modal.style.display = "none";
    }

    // زر تغيير كلمة المرور
    if (changePasswordBtn) {
        changePasswordBtn.addEventListener("click", openModal);
    }

    // أيقونات التعديل
    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("action-btn")) {
            openModal();
        }
    });

    // زر إلغاء
    if (cancelBtn) {
        cancelBtn.addEventListener("click", closeModal);
    }

    // إغلاق عند الضغط خارج النافذة
    modal.addEventListener("click", function (e) {
        if (e.target === modal) {
            closeModal();
        }
    });

});
