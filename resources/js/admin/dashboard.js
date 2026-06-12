// =================
// REAL-TIME ADD BOOK
// =================
function createBookRow(data, index = 0) {
    return `
    <div class="book-row-wrapper">
        <div class="row-number">?</div>

        <div class="book-row"
            data-ulasan="[]"
            data-rating-summary='{"rating_average":0,"rating_distribution":{"1":0,"2":0,"3":0,"4":0,"5":0}}'
            data-book-id="${data.BukuID}"
            data-judul="${data.Judul}"
            data-penulis="${data.Penulis}"
            data-penerbit="${data.Penerbit}"
            data-tahun="${data.TahunTerbit}"
            data-kategori="${data.KategoriText}"
            data-deskripsi="${data.Deskripsi}"
            data-stok="${data.Stok}"
            data-cover="${data.CoverUrl}"
            data-dipinjam="0"
        >
            <div class="book-cover-thumb">
    <img src="${data.CoverUrl}">
</div>

<div class="book-title">
    ${data.Judul}
</div>

<div class="book-small">
    ${data.Penulis}
</div>

<div class="book-category">
    ${data.KategoriText}
</div>

<div class="book-stock">
    ${data.Stok}
</div>

<div class="book-borrowed">
    0
</div>

        </div>
    </div>`;
}

// =================
// RATING DAN ULASAN
// =================
function renderRatingSummary(data) {
    const avgEl = document.getElementById("avgRating");

    const dist = data.rating_distribution;

    const total = dist[5] + dist[4] + dist[3] + dist[2] + dist[1];

    const avg = data.rating_average || 0;

    avgEl.innerText = avg.toFixed(1).replace(".", ",");

    for (let i = 5; i >= 1; i--) {
        const percent = total === 0 ? 0 : (dist[i] / total) * 100;

        const bar = document.getElementById(`bar-${i}`);
        const count = document.getElementById(`count-${i}`);

        if (bar) {
            bar.style.width = `${percent}%`;
        }

        if (count) {
            count.innerText = dist[i] ?? 0;
        }
    }
}

// ========
// NOTIFIKASI
// ========
function showCollectionToast(message, type = "success") {
    const toast = document.getElementById("collectionToast");

    const text = document.getElementById("collectionToastText");

    const icon = document.getElementById("collectionToastIcon");

    text.innerText = message;

    // reset class
    toast.classList.remove("success", "error");

    // tipe notif
    if (type === "success") {
        toast.classList.add("success");

        icon.src = "/Flaticon/checked.png";
    } else {
        toast.classList.add("error");

        icon.src = "/Flaticon/close.png";
    }

    // show
    toast.classList.add("active");

    clearTimeout(window.collectionToastTimeout);

    window.collectionToastTimeout = setTimeout(() => {
        toast.classList.remove("active");
    }, 2200);
}

document.addEventListener("DOMContentLoaded", () => {
    const detail = document.getElementById("bookDetail");
    const closeDetail = document.getElementById("closeDetail");

    const detailCenter = document.getElementById("detailCenter");
    const detailRight = document.querySelector(".detail-right");

    const btnEdit = document.getElementById("btnEditDetail");
    const btnSave = document.getElementById("btnSaveDetail");
    const btnCancel = document.getElementById("btnCancelDetail");

    const deleteModal = document.getElementById("deleteBookModal");
    const cancelDelete = document.getElementById("cancelDeleteBook");
    const confirmDelete = document.getElementById("confirmDeleteBook");
    const searchInput = document.getElementById("bookSearchInput");

    const suggestionBox = document.getElementById("bookSearchSuggestions");

    let selectedRow = null;
    let selectedBookId = null;
    let activeRow = null;

    if (searchInput && suggestionBox) {
        let debounce;

        searchInput.addEventListener("input", () => {
            clearTimeout(debounce);

            const keyword = searchInput.value.trim();

            if (!keyword) {
                suggestionBox.innerHTML = "";
                suggestionBox.classList.remove("active");
                return;
            }

            debounce = setTimeout(async () => {
                try {
                    const response = await fetch(
                        `/admin/dashboard/suggestions?q=${keyword}`,
                    );

                    const books = await response.json();

                    if (!books.length) {
                        suggestionBox.innerHTML = "";
                        suggestionBox.classList.remove("active");
                        return;
                    }

                    suggestionBox.innerHTML = books
                        .map(
                            (book) => `
                    <div
                        class="suggestion-item"
                        data-title="${book.Judul}"
                    >
                        <div class="suggestion-cover">
                            <img
                                src="/storage/books/${book.Cover}"
                                alt="${book.Judul}"
                            >
                        </div>

                        <div class="suggestion-info">
                            <div class="suggestion-name">
                                ${book.Judul}
                            </div>

                            <div class="suggestion-author">
                                ${book.Penulis}
                            </div>
                        </div>
                    </div>
                `,
                        )
                        .join("");

                    suggestionBox.classList.add("active");
                } catch (error) {
                    console.error(error);
                }
            }, 250);
        });

        suggestionBox.addEventListener("click", (e) => {
            const item = e.target.closest(".suggestion-item");

            if (!item) return;

            searchInput.value = item.dataset.title;

            suggestionBox.classList.remove("active");
        });

        document.addEventListener("click", (e) => {
            if (
                !searchInput.contains(e.target) &&
                !suggestionBox.contains(e.target)
            ) {
                suggestionBox.classList.remove("active");
            }
        });
    }

    /* =========================
       RENDER
    ========================= */

    function renderView(data) {
        document.getElementById("vJudul").textContent = data.judul;

        document.getElementById("vPenulis").textContent = data.penulis;

        document.getElementById("vPenerbit").textContent = data.penerbit;

        document.getElementById("vTahun").textContent = data.tahun;

        document.getElementById("vKategori").textContent = data.kategori;

        document.getElementById("vDeskripsi").textContent = data.deskripsi;

        document.getElementById("detailTitle").textContent = data.judul;

        document.getElementById("detailImage").src = data.cover;

        document.getElementById("dStok").textContent = data.stok;

        document.getElementById("dStatus").textContent = data.dipinjam;
    }

    function renderReviews(ulasanData) {
        const container = document.getElementById("reviewsContainer");

        if (!container) return;

        container.innerHTML = "";

        if (!ulasanData || ulasanData.length === 0) {
            container.innerHTML = `
            <div class="empty-review">
                Jadilah yang pertama memberi ulasan!
            </div>
        `;

            return;
        }

        ulasanData.forEach((review) => {
            let starsHTML = "";

            for (let i = 0; i < review.rating; i++) {
                starsHTML += `
                <img
                    src="/Flaticon/star (1).png"
                    class="review-star-icon"
                >
            `;
            }

            const reviewHTML = `
            <div class="review-item">

                <div class="review-header">

                    <img
                        src="${review.foto}"
                        class="review-avatar"
                    >

                    <div class="review-user">

                        <h4>${review.nama}</h4>

                        <span>@${review.username}</span>

                    </div>

                    <div class="review-time">
                        ${review.created_at}
                    </div>

                </div>

                <div class="review-stars">
                    ${starsHTML}
                </div>

                <p class="review-text">
                    ${review.ulasan ?? ""}
                </p>

            </div>
        `;

            container.insertAdjacentHTML("beforeend", reviewHTML);
        });
    }

    function fillInputs(data) {
        document.getElementById("eJudul").value = data.judul;

        document.getElementById("ePenulis").value = data.penulis;

        document.getElementById("ePenerbit").value = data.penerbit;

        document.getElementById("eTahun").value = data.tahun;

        document.getElementById("eKategori").value = data.kategori;

        document.getElementById("eDeskripsi").value = data.deskripsi;
    }

    function openEdit() {
        if (!activeRow) return;

        fillInputs(activeRow.dataset);
        detailCenter.classList.add("editing");
        detailRight.classList.add("editing");
    }

    function closeEdit() {
        detailCenter.classList.remove("editing");
        detailRight.classList.remove("editing");
    }

    function openDetail(row) {
        activeRow = row;

        const data = row.dataset;

        renderView(data);

        closeEdit();

        detail.classList.add("active");
    }

    /* =========================
       OPEN DETAIL
    ========================= */

    document.addEventListener("click", (e) => {
        const row = e.target.closest(".book-row");
        if (!row) return;

        const data = row.dataset;

        const ulasanData = JSON.parse(data.ulasan || "[]");

        renderReviews(ulasanData);

        renderRatingSummary(
            data.ratingSummary ? JSON.parse(data.ratingSummary) : null,
        );

        openDetail(row);
    });

    if (closeDetail) {
        closeDetail.addEventListener("click", () => {
            detail.classList.remove("active");
            closeEdit();
        });
    }

    /* =========================
       EDIT DARI DETAIL
    ========================= */

    if (btnEdit) {
        btnEdit.addEventListener("click", openEdit);
    }

    if (btnCancel) {
        btnCancel.addEventListener("click", closeEdit);
    }

    if (btnSave) {
        btnSave.addEventListener("click", () => {
            if (!activeRow) return;

            const data = activeRow.dataset;

            data.judul = document.getElementById("eJudul").value;

            data.penulis = document.getElementById("ePenulis").value;

            data.penerbit = document.getElementById("ePenerbit").value;

            data.tahun = document.getElementById("eTahun").value;

            data.kategori = document.getElementById("eKategori").value;

            data.deskripsi = document.getElementById("eDeskripsi").value;

            renderView(data);

            closeEdit();
        });
    }

    /* =========================
       HAPUS DARI DETAIL
    ========================= */

    const detailDeleteBtn = document.querySelector(".btn-delete-detail");

    if (detailDeleteBtn) {
        detailDeleteBtn.addEventListener("click", (e) => {
            e.stopPropagation();

            if (!activeRow) return;

            selectedRow = activeRow;
            selectedBookId = activeRow.dataset.bookId;

            deleteModal.classList.add("active");
        });
    }

    /* =========================
       CANCEL DELETE
    ========================= */

    if (cancelDelete) {
        cancelDelete.addEventListener("click", () => {
            deleteModal.classList.remove("active");
            selectedRow = null;
            selectedBookId = null;
        });
    }

    /* =========================
       CONFIRM DELETE
    ========================= */

    if (confirmDelete) {
        confirmDelete.addEventListener("click", async () => {
            if (!selectedBookId) return;

            try {
                const response = await fetch(`/admin/books/${selectedBookId}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                        Accept: "application/json",
                    },
                });

                const result = await response.json();

                if (result.success) {
                    const wrapper = selectedRow.closest(".book-row-wrapper");

                    if (wrapper) wrapper.remove();
                    showCollectionToast("Buku berhasil dihapus", "success");

                    deleteModal.classList.remove("active");
                    detail.classList.remove("active");

                    selectedRow = null;
                    selectedBookId = null;
                    activeRow = null;
                }
            } catch (error) {
                console.error(error);
            }
        });
    }

    /* =========================
   SEARCH + FILTER
========================= */

    const searchButton = document.querySelector(".btn-search");

    const categoryButtons = document.querySelectorAll(".categories button");

    const rows = document.querySelectorAll(".book-row-wrapper");

    const emptySearch = document.getElementById("emptySearch");

    let activeCategories = ["Semua"];

    /* =========================
   FILTER FUNCTION
========================= */
    function filterBooks() {
        const keyword = searchInput.value.toLowerCase();

        const rows = document.querySelectorAll(".book-row-wrapper"); // pindahkan ke sini

        let visibleCount = 0;

        rows.forEach((row) => {
            const bookRow = row.querySelector(".book-row");

            const title = bookRow.dataset.judul.toLowerCase();
            const kategori = bookRow.dataset.kategori.toLowerCase();

            const matchSearch = title.includes(keyword);

            let matchCategory = true;

            if (!activeCategories.includes("Semua")) {
                matchCategory = activeCategories.some((cat) =>
                    kategori.includes(cat.toLowerCase()),
                );
            }

            if (matchSearch && matchCategory) {
                row.style.display = "flex";
                visibleCount++;
            } else {
                row.style.display = "none";
            }
        });

        emptySearch.classList.toggle("active", visibleCount === 0);
    }
    /* =========================
   SEARCH INPUT
========================= */

    if (searchButton) {
        searchButton.addEventListener("click", (e) => {
            e.preventDefault();
            filterBooks();
        });
    }

    /* =========================
   MULTI CATEGORY FILTER
========================= */

    categoryButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
            const category = btn.textContent.trim();

            // =========================
            // BUTTON SEMUA
            // =========================

            if (category === "Semua") {
                activeCategories = ["Semua"];

                categoryButtons.forEach((b) => {
                    if (b.textContent.trim() === "Semua") {
                        b.classList.add("active");
                    } else {
                        b.classList.remove("active");
                    }
                });
            }

            // =========================
            // BUTTON KATEGORI
            // =========================
            else {
                activeCategories = activeCategories.filter(
                    (c) => c !== "Semua",
                );

                const allButton = [...categoryButtons].find(
                    (b) => b.textContent.trim() === "Semua",
                );

                if (allButton) {
                    allButton.classList.remove("active");
                }

                // toggle active
                if (activeCategories.includes(category)) {
                    activeCategories = activeCategories.filter(
                        (c) => c !== category,
                    );

                    btn.classList.remove("active");
                } else {
                    activeCategories.push(category);

                    btn.classList.add("active");
                }

                // kalau kosong -> balik ke semua
                if (activeCategories.length === 0) {
                    activeCategories = ["Semua"];

                    if (allButton) {
                        allButton.classList.add("active");
                    }
                }
            }

            filterBooks();
        });
    });

    /* =========================
   ADD BOOK
========================= */

    const addBookOverlay = document.getElementById("addBookOverlay");

    const openAddBook = document.getElementById("openAddBook");

    const closeAddBook = document.getElementById("closeAddBook");

    if (openAddBook) {
        openAddBook.addEventListener("click", () => {
            addBookOverlay.classList.add("active");
        });
    }

    if (closeAddBook) {
        closeAddBook.addEventListener("click", () => {
            addBookOverlay.classList.remove("active");
        });
    }

    /* =========================
   COVER PREVIEW
========================= */

    const coverInput = document.getElementById("bookCoverInput");

    const coverPreview = document.getElementById("coverPreview");

    const uploadText = document.getElementById("uploadText");

    if (coverInput) {
        coverInput.addEventListener("change", function () {
            const file = this.files[0];

            if (!file) return;

            const reader = new FileReader();

            reader.onload = function (e) {
                coverPreview.src = e.target.result;

                coverPreview.style.display = "block";

                uploadText.style.display = "none";
            };

            reader.readAsDataURL(file);
        });
    }

    /* =========================
   SUBMIT ADD BOOK
========================= */

    const submitAddBook = document.getElementById("submitAddBook");

    if (submitAddBook) {
        submitAddBook.addEventListener("click", async () => {
            try {
                const formData = new FormData();

                formData.append(
                    "Judul",
                    document.getElementById("addJudul").value,
                );

                formData.append(
                    "Penulis",
                    document.getElementById("addPenulis").value,
                );

                formData.append(
                    "Penerbit",
                    document.getElementById("addPenerbit").value,
                );

                formData.append(
                    "TahunTerbit",
                    document.getElementById("addTahun").value,
                );

                formData.append(
                    "Deskripsi",
                    document.getElementById("addDeskripsi").value,
                );

                formData.append(
                    "Stok",
                    document.getElementById("addStock").value,
                );

                const kategoriSelect = document.getElementById("addKategori");

                const selectedKategori = [
                    ...kategoriSelect.selectedOptions,
                ].map((option) => option.value);

                selectedKategori.forEach((id) => {
                    formData.append("KategoriID[]", id);
                });

                formData.append("Cover", coverInput.files[0]);

                const response = await fetch("/admin/books", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                        Accept: "application/json",
                    },
                    body: formData,
                });

                const result = await response.json();

                if (!response.ok) {
                    throw result;
                }

                if (result.success) {
                    showCollectionToast("Buku berhasil ditambahkan", "success");

                    document.getElementById("addJudul").value = "";
                    document.getElementById("addPenulis").value = "";
                    document.getElementById("addPenerbit").value = "";
                    document.getElementById("addTahun").value = "";
                    document.getElementById("addDeskripsi").value = "";
                    document.getElementById("addStock").value = 1;

                    coverPreview.style.display = "none";
                    uploadText.style.display = "block";
                    coverInput.value = "";

                    if (result.success) {
                        showCollectionToast(
                            "Buku berhasil ditambahkan",
                            "success",
                        );

                        const kategoriSelect =
                            document.getElementById("addKategori");

                        const kategoriText = [...kategoriSelect.selectedOptions]
                            .map((opt) => opt.textContent)
                            .join(", ");

                        const newData = {
                            ...result.data,
                            KategoriText: kategoriText,
                            CoverUrl: result.data.Cover
                                ? `/storage/books/${result.data.Cover}`
                                : "/images/default-book.png",
                        };

                        const container =
                            document.querySelector(
                                ".book-row-wrapper",
                            ).parentElement;

                        container.insertAdjacentHTML(
                            "afterbegin",
                            createBookRow(newData),
                        );

                        // reset form
                        document.getElementById("addJudul").value = "";
                        document.getElementById("addPenulis").value = "";
                        document.getElementById("addPenerbit").value = "";
                        document.getElementById("addTahun").value = "";
                        document.getElementById("addDeskripsi").value = "";
                        document.getElementById("addStock").value = 1;

                        coverPreview.style.display = "none";
                        uploadText.style.display = "block";
                        coverInput.value = "";
                    }

                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);
                } else {
                    showCollectionToast(
                        result.message || "Gagal menambahkan buku",
                        "error",
                    );
                }
            } catch (error) {
                console.error(error);

                let message = "Terjadi kesalahan";

                if (error.errors) {
                    message = Object.values(error.errors)[0][0];
                }

                showCollectionToast(message, "error");
            }
        });
    }
});
