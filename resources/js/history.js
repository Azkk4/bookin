document.addEventListener("DOMContentLoaded", () => {
    const historyModal = document.getElementById("historyModal");

    const openHistory = document.getElementById("openHistory");

    const closeHistory = document.getElementById("closeHistory");

    const receiptModal = document.getElementById("receiptModal");

    if (!historyModal || !openHistory || !closeHistory) {
        return;
    }

    // =========================
    // BUKA HISTORY
    // =========================

    openHistory.addEventListener("click", () => {
        historyModal.classList.add("active");
        lockBody();
    });

    // =========================
    // CLOSE HISTORY
    // =========================

    closeHistory.addEventListener("click", () => {
        historyModal.classList.remove("active");
        unlockBody();
    });

    // =========================
    // KLIK HISTORY
    // =========================

    document.querySelectorAll(".receipt-trigger").forEach((item) => {
        item.addEventListener("click", () => {
            historyModal.classList.remove("active");

            // =====================
            // AMBIL DATA
            // =====================

            const judul = item.dataset.judul;

            const penulis = item.dataset.penulis;

            const penerbit = item.dataset.penerbit;

            const tahun = item.dataset.tahun;

            const cover = item.dataset.cover;

            const pinjam = item.dataset.pinjam;

            const kembali = item.dataset.kembali;

            const status = item.dataset.status;

            // =====================
            // ISI STRUK
            // =====================

            document.getElementById("rJudul").innerText = judul;

            document.getElementById("rPenulis").innerText = penulis;

            document.getElementById("rPenerbit").innerText = penerbit;

            document.getElementById("rTahun").innerText = tahun;

            document.getElementById("rTanggal").innerText = pinjam;

            document.getElementById("rKembali").innerText = kembali;

            document.getElementById("receiptBookImage").src = cover;

            const successText = document.querySelector(".success-text");

            if (successText) {
                successText.innerText = status;
            }

            const receiptStatusTitle =
                document.getElementById("receiptStatusTitle");

            if (receiptStatusTitle) {
                if (status === "Dikembalikan") {
                    receiptStatusTitle.innerText = "Berhasil dikembalikan";
                } else {
                    receiptStatusTitle.innerText = "Berhasil dipinjam";
                }
            }

            // =====================
            // HITUNG DURASI
            // =====================

            const start = new Date(pinjam.split("/").reverse().join("-"));

            const end = new Date(kembali.split("/").reverse().join("-"));

            const diffTime = end.getTime() - start.getTime();

            const diffDays = diffTime / (1000 * 60 * 60 * 24);

            document.getElementById("rDurasi").innerText = diffDays + " Hari";

            // =====================
            // BUKA STRUK
            // =====================

            receiptModal.classList.add("active");
            lockBody();
        });
    });
});
