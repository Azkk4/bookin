document.addEventListener("DOMContentLoaded", () => {
    const borrowModal = document.getElementById("borrowModal");
    const receiptModal = document.getElementById("receiptModal");

    const borrowDate = document.getElementById("borrowDate");
    const borrowDuration = document.getElementById("borrowDuration");
    const returnDate = document.getElementById("returnDate");

    const btnPinjam = document.getElementById("detailActionButton");
    const btnConfirmBorrow = document.querySelector(".btn-confirm-borrow");

    const closeBorrow = document.getElementById("closeBorrow");

    // =========================
    // OPEN MODAL
    // =========================

    // =========================
    // CLOSE MODAL
    // =========================

    if (closeBorrow) {
        closeBorrow.addEventListener("click", () => {
            borrowModal.classList.remove("active");
        });
    }

    // =========================
    // DEFAULT DATE
    // =========================
    if (!borrowDate || !borrowDuration || !returnDate) {
        return;
    }

    const today = new Date();

    borrowDate.value = today.toISOString().split("T")[0];

    function formatDate(date) {
        const day = String(date.getDate()).padStart(2, "0");

        const month = String(date.getMonth() + 1).padStart(2, "0");

        const year = date.getFullYear();

        return `${day}/${month}/${year}`;
    }

    function updateReturnDate() {
        const selectedDate = new Date(borrowDate.value);

        const duration = parseInt(borrowDuration.value);

        selectedDate.setDate(selectedDate.getDate() + duration);

        returnDate.value = formatDate(selectedDate);
    }

    updateReturnDate();

    borrowDate.addEventListener("change", updateReturnDate);

    borrowDuration.addEventListener("change", updateReturnDate);

    // =========================
    // SUBMIT PINJAM
    // =========================

    if (btnConfirmBorrow) {
        btnConfirmBorrow.addEventListener("click", () => {
            openActionModal(
                "Anda yakin ingin meminjam buku ini?",
                {
                    button: "Pinjam",
                    class: "save-mode",
                },

                async () => {
                    try {
                        btnConfirmBorrow.disabled = true;

                        btnConfirmBorrow.innerText = "...";

                        const response = await fetch("/peminjaman/store", {
                            method: "POST",

                            headers: {
                                "Content-Type": "application/json",

                                "X-CSRF-TOKEN": document.querySelector(
                                    'meta[name="csrf-token"]',
                                ).content,
                            },

                            body: JSON.stringify({
                                BukuID: window.activeBorrowBook.id,

                                TanggalPeminjaman: borrowDate.value,

                                Durasi: borrowDuration.value,
                            }),
                        });

                        const result = await response.json();

                        // gagal
                        if (!result.success) {
                            showBorrowToast(result.message);

                            return;
                        }

                        // notif sukses
                        showBorrowToast(result.message);

                        // =========================
                        // BARU UPDATE UI
                        // =========================

                        // update dataset semua card
                        document
                            .querySelectorAll(
                                `[data-id="${window.activeBorrowBook.id}"]`,
                            )
                            .forEach((el) => {
                                el.dataset.dipinjam = "true";
                                el.dataset.stok = result.data.stok;
                                el.dataset.peminjaman =
                                    result.data.peminjaman_id;
                            });

                        // update stok detail
                        const stokElement = document.getElementById("dStok");

                        if (stokElement) {
                            stokElement.innerText = result.data.stok;
                        }

                        // update tombol realtime
                        updateBorrowButton(
                            true,
                            window.activeBorrowBook.id,
                            result.data.peminjaman_id,
                        );

                        // =========================
                        // ISI STRUK
                        // =========================

                        const receiptBookImage =
                            document.getElementById("receiptBookImage");

                        if (receiptBookImage) {
                            receiptBookImage.src = result.data.cover;
                        }

                        const rJudul = document.getElementById("rJudul");
                        if (rJudul) rJudul.innerText = result.data.judul;

                        const rPenulis = document.getElementById("rPenulis");
                        if (rPenulis) rPenulis.innerText = result.data.penulis;

                        const rPenerbit = document.getElementById("rPenerbit");
                        if (rPenerbit)
                            rPenerbit.innerText = result.data.penerbit;

                        const rTahun = document.getElementById("rTahun");
                        if (rTahun) rTahun.innerText = result.data.tahun;

                        const rTanggal = document.getElementById("rTanggal");
                        if (rTanggal)
                            rTanggal.innerText = result.data.tanggal_pinjam;

                        const rDurasi = document.getElementById("rDurasi");
                        if (rDurasi) rDurasi.innerText = result.data.durasi;

                        const rKembali = document.getElementById("rKembali");
                        if (rKembali)
                            rKembali.innerText = result.data.tanggal_kembali;

                        // tutup modal pinjam
                        if (borrowModal) {
                            borrowModal.classList.remove("active");
                            unlockBody();
                        }

                        // buka modal struk
                        if (receiptModal) {
                            receiptModal.classList.add("active");
                            lockBody();
                        }
                    } catch (error) {
                        console.log(error);

                        alert(error.message);
                    } finally {
                        btnConfirmBorrow.disabled = false;

                        btnConfirmBorrow.innerText = "Pinjam";
                    }
                },
            );
        });
    }

    // =========================
    // CLOSE STRUK
    // =========================

    const closeReceipt = document.getElementById("closeReceipt");

    if (closeReceipt) {
        closeReceipt.addEventListener("click", () => {
            receiptModal.classList.remove("active");
            unlockBody();

            window.location.reload();
        });
    }
});
