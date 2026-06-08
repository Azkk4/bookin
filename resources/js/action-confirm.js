document.addEventListener("DOMContentLoaded", () => {
    const actionModal = document.getElementById("actionConfirmModal");

    if (!actionModal) return;

    const actionText = document.getElementById("actionConfirmText");

    const confirmBtn = document.getElementById("confirmActionBtn");

    const cancelBtn = document.getElementById("cancelActionConfirm");

    let confirmCallback = null;

    window.openActionModal = (text, mode, callback, cancelCallback = null) => {
        actionText.innerHTML = text;

        confirmBtn.textContent = mode.button;

        confirmBtn.classList.remove(
            "save-mode",
            "discard-mode",
            "disable-mode",
            "login-mode",
        );

        confirmBtn.classList.add(mode.class);

        confirmCallback = callback;

        window.cancelActionCallback = cancelCallback;

        actionModal.classList.add("active");
        lockBody();
    };

    function closeModal() {
        actionModal.classList.remove("active");
        unlockBody();
    }

    cancelBtn.addEventListener("click", () => {
        if (window.cancelActionCallback) {
            window.cancelActionCallback();
        }

        closeModal();
    });

    actionModal.addEventListener("click", (e) => {
        if (e.target === actionModal) {
            closeModal();
        }
    });

    confirmBtn.addEventListener("click", () => {
        if (confirmCallback) {
            confirmCallback();
        }

        closeModal();
    });
});
