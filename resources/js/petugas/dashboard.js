document.addEventListener("DOMContentLoaded", () => {
    const rows = document.querySelectorAll(".book-row");
    const detail = document.getElementById("bookDetail");
    const closeBtn = document.getElementById("closeDetail");
    let initialStockValue = "";

    rows.forEach((row) => {
        row.addEventListener("click", () => {
            document.getElementById("detailTitle").textContent =
                row.dataset.judul;

            document.getElementById("detailImage").src = row.dataset.cover;

            document.getElementById("vJudul").textContent = row.dataset.judul;

            document.getElementById("vPenulis").textContent =
                row.dataset.penulis;

            document.getElementById("vPenerbit").textContent =
                row.dataset.penerbit;

            document.getElementById("vTahun").textContent = row.dataset.tahun;

            document.getElementById("vKategori").textContent =
                row.dataset.kategori;

            document.getElementById("vDeskripsi").textContent =
                row.dataset.deskripsi;

            stockInput.value = row.dataset.stok;

            initialStockValue = row.dataset.stok;

            detail.classList.add("active");
        });
    });

    function isStockChanged() {
        const stockInput = document.querySelector(".stock-input");

        return stockInput.value !== initialStockValue;
    }

    closeBtn.addEventListener("click", () => {
        // jika stok berubah
        if (isStockChanged()) {
            openActionModal(
                "Anda yakin ingin membuang perubahan stok?",
                {
                    button: "Buang",
                    class: "discard-mode",
                },
                () => {
                    // reset ke stok awal
                    stockInput.value = initialStockValue;

                    detail.classList.remove("active");
                },
            );

            return;
        }

        // jika tidak ada perubahan
        detail.classList.remove("active");
    });

    // ===================
    // MODALS CONFIRM STOK
    // ===================

    const saveStockBtn = document.querySelector(".btn-save-detail");

    const stockInput = document.querySelector(".stock-input");

    saveStockBtn.addEventListener("click", () => {
        // jika tidak ada perubahan
        if (!isStockChanged()) {
            return;
        }

        openActionModal(
            "Anda yakin ingin mengubah stok?",
            {
                button: "Simpan",
                class: "save-mode",
            },
            () => {
                const newStock = stockInput.value;

                console.log("stok baru:", newStock);

                // update stok awal
                initialStockValue = newStock;

                /*
                fetch Laravel disini
            */
            },
        );
    });

    /* =========================
   CATEGORY FILTER
========================= */

    const categoryButtons = document.querySelectorAll(".categories button");

    const allButton = document.querySelector(".categories .active");

    categoryButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const isAll = button.textContent.trim() === "Semua";

            // =====================
            // JIKA TOMBOL SEMUA
            // =====================

            if (isAll) {
                categoryButtons.forEach((btn) => {
                    btn.classList.remove("active");
                });

                button.classList.add("active");

                return;
            }

            // =====================
            // JIKA KATEGORI BIASA
            // =====================

            allButton.classList.remove("active");

            button.classList.toggle("active");

            // jika tidak ada kategori aktif,
            // kembali ke "Semua"

            const activeCategories = [...categoryButtons].filter((btn) => {
                return (
                    btn.classList.contains("active") &&
                    btn.textContent.trim() !== "Semua"
                );
            });

            if (activeCategories.length === 0) {
                allButton.classList.add("active");
            }
        });
    });
});
