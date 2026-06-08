document.addEventListener("DOMContentLoaded", () => {
    const receiptModal = document.getElementById("receiptModal");
    const closeReceipt = document.getElementById("closeReceipt");

    const borrowModal = document.getElementById("borrowModal");

    const confirmBorrowBtn = document.querySelector(".btn-confirm-borrow");

    const borrowDate = document.getElementById("borrowDate");
    const borrowDuration = document.getElementById("borrowDuration");
    const returnDate = document.getElementById("returnDate");

    // format tanggal
    function formatDate(date) {
        const day = String(date.getDate()).padStart(2, "0");
        const month = String(date.getMonth() + 1).padStart(2, "0");
        const year = date.getFullYear();

        return `${day}/${month}/${year}`;
    }

    if (!receiptModal || !closeReceipt) {
        return;
    }

    // close struk
    closeReceipt.addEventListener("click", () => {
        receiptModal.classList.remove("active");
        unlockBody();
    });
});
