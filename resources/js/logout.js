// ======================
// LOGOUT MODAL
// ======================
document.addEventListener("DOMContentLoaded", () => {
    const logoutModal = document.getElementById("logoutModal");
    const openLogout = document.getElementById("openLogout");
    const cancelLogout = document.getElementById("cancelLogout");

    if (openLogout && logoutModal) {
        openLogout.addEventListener("click", () => {
            logoutModal.classList.add("active");
            lockBody();
        });
    }

    if (cancelLogout && logoutModal) {
        cancelLogout.addEventListener("click", () => {
            logoutModal.classList.remove("active");
            unlockBody();
        });
    }

    if (logoutModal) {
        logoutModal.addEventListener("click", (e) => {
            if (e.target === logoutModal) {
                logoutModal.classList.remove("active");
                unlockBody();
            }
        });
    }
});
