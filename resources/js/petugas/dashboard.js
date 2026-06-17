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

    function updateRowNumbers() {
        let nomor = 1;

        document.querySelectorAll(".book-row-wrapper").forEach((wrapper) => {
            if (wrapper.style.display !== "none") {
                const rowNumber = wrapper.querySelector(".row-number");

                if (rowNumber) {
                    rowNumber.textContent = nomor;

                    nomor++;
                }
            }
        });
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

    const allButton = document.querySelector(".categories button:first-child");

    const bookWrappers = document.querySelectorAll(".book-row-wrapper");

    const emptySearch = document.getElementById("emptySearch");

    categoryButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const isAll = button.textContent.trim() === "Semua";

            // =====================
            // TOMBOL SEMUA
            // =====================

            if (isAll) {
                categoryButtons.forEach((btn) => {
                    btn.classList.remove("active");
                });

                allButton.classList.add("active");

                bookWrappers.forEach((book) => {
                    book.style.display = "flex";
                });

                emptySearch.style.display = "none";

                return;
            }

            // =====================
            // KATEGORI BIASA
            // =====================

            allButton.classList.remove("active");

            button.classList.toggle("active");

            const activeCategories = [...categoryButtons]
                .filter(
                    (btn) =>
                        btn.classList.contains("active") &&
                        btn.textContent.trim() !== "Semua",
                )
                .map((btn) => btn.textContent.trim().toLowerCase());

            if (activeCategories.length === 0) {
                allButton.classList.add("active");

                bookWrappers.forEach((book) => {
                    book.style.display = "flex";
                });

                emptySearch.style.display = "none";

                return;
            }

            // FILTER BUKU

            let visibleCount = 0;

            bookWrappers.forEach((book) => {
                const row = book.querySelector(".book-row");

                const categories = row.dataset.kategori.toLowerCase();

                const match = activeCategories.some((cat) =>
                    categories.includes(cat),
                );

                if (match) {
                    book.style.display = "flex";

                    visibleCount++;
                } else {
                    book.style.display = "none";
                }
            });

            emptySearch.style.display = visibleCount === 0 ? "block" : "none";

            updateRowNumbers();
        });
    });
});
