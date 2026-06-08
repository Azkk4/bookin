window.lockBody = function () {
    document.body.classList.add("modal-open");
};

window.unlockBody = function () {
    const activeOverlay = document.querySelector(
        `
        .book-detail-overlay.active,
        .logout-modal-overlay.active,
        .borrow-modal-overlay.active,
        .history-modal-overlay.active,
        .pengembalian-modal-overlay.active,
        .action-modal-overlay.active
        `,
    );

    // hanya unlock kalau sudah tidak ada modal aktif
    if (!activeOverlay) {
        document.body.classList.remove("modal-open");
    }
};

window.updateBorrowButton = function (isDipinjam, bukuID, peminjamanID = null) {
    const btn = document.getElementById("detailActionButton");

    if (!btn) return;

    // reset class dulu
    btn.classList.remove("btn-kembalikan-detail");

    // reset onclick
    btn.onclick = null;

    // =========================
    // MODE KEMBALIKAN
    // =========================

    if (isDipinjam) {
        btn.innerText = "Kembalikan";

        btn.classList.add("btn-kembalikan-detail");

        btn.onclick = async () => {
            openActionModal(
                "Anda yakin ingin mengembalikan buku ini?",
                {
                    button: "Kembalikan",
                    class: "discard-mode",
                },

                async () => {
                    try {
                        const response = await fetch(
                            `/pengembalian/${peminjamanID}`,
                            {
                                method: "POST",

                                headers: {
                                    "X-CSRF-TOKEN": document.querySelector(
                                        'meta[name="csrf-token"]',
                                    ).content,
                                },
                            },
                        );

                        const result = await response.json();

                        if (!result.success) {
                            showBorrowToast(result.message);
                            return;
                        }

                        // update semua dataset
                        document
                            .querySelectorAll(`[data-id="${bukuID}"]`)
                            .forEach((el) => {
                                el.dataset.dipinjam = "false";

                                el.dataset.stok = result.data.stok;
                            });

                        // update stok detail
                        document.getElementById("dStok").innerText =
                            result.data.stok;

                        // update tombol realtime
                        updateBorrowButton(false, bukuID);

                        showBorrowToast(result.message);

                        setTimeout(() => {
                            window.location.reload();
                        }, 1200);
                    } catch (error) {
                        console.log(error);
                    }
                },
            );
        };
    }

    // =========================
    // MODE PINJAM
    // =========================
    else {
        btn.innerText = "Pinjam";

        btn.onclick = () => {
            const isGuest = document.body.dataset.guest === "true";

            if (isGuest) {
                requireLogin();
                return;
            }

            const borrowModal = document.getElementById("borrowModal");

            if (borrowModal) {
                borrowModal.classList.add("active");
                lockBody();
            }
        };
    }
};

// Function Koleksi
function updateKoleksiUI(bukuID, isKoleksi) {
    // ambil semua elemen buku dengan id sama
    const relatedElements = document.querySelectorAll(`[data-id="${bukuID}"]`);

    // ambil data buku dari salah satu card
    const sourceCard = relatedElements[0];

    if (!sourceCard) return;

    // =========================
    // UPDATE DATASET
    // =========================

    relatedElements.forEach((el) => {
        el.dataset.koleksi = isKoleksi ? "true" : "false";
    });

    // =========================
    // UPDATE BUTTON MODAL
    // =========================

    const btnAdd = document.getElementById("btnAddCollection");

    if (btnAdd) {
        if (isKoleksi) {
            btnAdd.innerHTML = `
                <img src="/Flaticon/trash.png">
                Hapus dari Koleksi
            `;

            btnAdd.classList.add("remove-mode");
        } else {
            btnAdd.innerHTML = `
                <img src="/Flaticon/plus.png">
                Tambah ke Koleksi
            `;

            btnAdd.classList.remove("remove-mode");
        }
    }

    // =========================
    // SIDEBAR KOLEKSI
    // =========================

    const collectionContent = document.querySelector(".collection-content");

    // kalau ditambahkan ke koleksi
    if (isKoleksi) {
        // cek apakah sudah ada di sidebar
        const existingSlot = document.querySelector(
            `.book-slot[data-id="${bukuID}"]`,
        );

        // kalau belum ada -> tambahkan
        if (!existingSlot) {
            const slot = document.createElement("div");

            slot.className = "book-slot";

            // copy semua dataset
            Object.keys(sourceCard.dataset).forEach((key) => {
                slot.dataset[key] = sourceCard.dataset[key];
            });

            slot.innerHTML = `
                <input
                    type="checkbox"
                    class="collection-check"
                    value="${bukuID}"
                >

                <img
                    src="${sourceCard.dataset.cover}"
                    class="slot-cover"
                >

                <div class="slot-info">
                    <h4>${sourceCard.dataset.judul}</h4>
                    <p>${sourceCard.dataset.penulis}</p>
                    <span>${sourceCard.dataset.kategori}</span>
                </div>
            `;

            collectionContent.appendChild(slot);

            // =========================
            // EVENT CLICK SLOT BARU
            // =========================

            slot.addEventListener("click", () => {
                if (sidebar.classList.contains("manage-mode")) {
                    return;
                }
                const detail = document.getElementById("bookDetail");

                const data = slot.dataset;

                document.getElementById("detailTitle").innerText = data.judul;

                document.getElementById("dJudul").innerText = data.judul;

                document.getElementById("dPenulis").innerText = data.penulis;

                document.getElementById("dPenerbit").innerText = data.penerbit;

                document.getElementById("dTahun").innerText = data.tahun;

                document.getElementById("dKategori").innerText = data.kategori;

                document.getElementById("dDeskripsi").innerText =
                    data.deskripsi;

                document.getElementById("dStok").innerText = data.stok;

                document.getElementById("detailImage").src = data.cover;

                const isKoleksi = slot.dataset.koleksi === "true";

                updateKoleksiUI(bukuID, isKoleksi);

                document.getElementById("btnAddCollection").onclick = () => {
                    toggleKoleksi(bukuID);
                };

                detail.classList.add("active");
                lockBody();
                setTimeout(() => {
                    document.getElementById("reviewText").focus();
                }, 250);
            });
        }
    }

    // kalau dihapus dari koleksi
    else {
        document
            .querySelectorAll(`.book-slot[data-id="${bukuID}"]`)
            .forEach((el) => {
                el.remove();
            });
    }
}

// Function Ulasan
window.renderReviews = function (ulasanData) {
    const container = document.getElementById("reviewsContainer");

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
};

window.renderRatingSummary = function (data) {
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
};

function showCollectionToast(message) {
    const toast = document.getElementById("collectionToast");

    const text = document.getElementById("collectionToastText");

    text.innerText = message;

    toast.classList.add("active");

    clearTimeout(window.collectionToastTimeout);

    window.collectionToastTimeout = setTimeout(() => {
        toast.classList.remove("active");
    }, 2200);
}

window.showBorrowToast = function (message) {
    const toast = document.getElementById("borrowToast");

    const text = document.getElementById("borrowToastText");

    text.innerText = message;

    toast.classList.add("active");

    clearTimeout(window.borrowToastTimeout);

    window.borrowToastTimeout = setTimeout(() => {
        toast.classList.remove("active");
    }, 2200);
};

window.requireLogin = function () {
    openActionModal(
        `
        <h2>Login diperlukan.</h2><br>
        Untuk menggunakan fitur ini,
        silakan login terlebih dahulu.
        `,
        {
            button: "Login",
            class: "login-mode",
        },

        () => {
            window.location.href = "/login";
        },
    );
};

async function toggleKoleksi(bukuID) {
    try {
        const response = await fetch("/koleksi/toggle", {
            method: "POST",

            headers: {
                "Content-Type": "application/json",

                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]',
                ).content,
            },

            body: JSON.stringify({
                BukuID: bukuID,
            }),
        });

        const result = await response.json();

        const isKoleksi = result.status === "added";

        updateKoleksiUI(bukuID, isKoleksi);

        if (isKoleksi) {
            showCollectionToast("Berhasil ditambahkan ke koleksi");
        } else {
            showCollectionToast("Berhasil dihapus dari koleksi");
        }
    } catch (error) {
        console.log(error);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    // kategori
    document.querySelectorAll(".categories button").forEach((btn) => {
        btn.addEventListener("click", () => {
            btn.classList.toggle("active");
        });
    });

    // =========================
    // SIDEBAR
    // =========================

    const sidebar = document.getElementById("sidebar");
    const toggleSidebar = document.getElementById("toggleSidebar");

    if (toggleSidebar && sidebar) {
        toggleSidebar.addEventListener("click", (e) => {
            e.stopPropagation();

            sidebar.classList.toggle("active");

            if (!sidebar.classList.contains("active")) {
                sidebar.classList.remove("manage-mode");

                document
                    .querySelectorAll(".collection-check")
                    .forEach((check) => {
                        check.checked = false;
                    });
            }
        });
    }

    // =========================
    // MODE KELOLA
    // =========================

    const manageBtn = document.getElementById("manageBtn");
    const manageText = document.getElementById("manageText");
    const deleteBtn = document.getElementById("deleteBtn");
    const cancelManage = document.getElementById("cancelManage");

    if (manageBtn) {
        manageBtn.addEventListener("click", (e) => {
            e.stopPropagation();

            sidebar.classList.toggle("manage-mode");

            const isManage = sidebar.classList.contains("manage-mode");

            // reset checkbox saat keluar
            if (!isManage) {
                document
                    .querySelectorAll(".collection-check")
                    .forEach((check) => {
                        check.checked = false;
                    });
            }
        });
    }

    if (cancelManage) {
        cancelManage.addEventListener("click", (e) => {
            e.stopPropagation();

            sidebar.classList.remove("manage-mode");

            document.querySelectorAll(".collection-check").forEach((check) => {
                check.checked = false;
            });
        });
    }

    const deleteSelectedBtn = document.getElementById("deleteBtn");

    if (deleteSelectedBtn) {
        deleteSelectedBtn.addEventListener("click", async (e) => {
            e.stopPropagation();

            // ambil semua checkbox yang dicentang
            const checkedBooks = [
                ...document.querySelectorAll(".collection-check:checked"),
            ];

            // kalau tidak ada yang dipilih
            if (checkedBooks.length === 0) {
                alert("Pilih buku terlebih dahulu");
                return;
            }

            // ambil semua BukuID
            const bukuIDs = checkedBooks.map((check) => check.value);

            try {
                const response = await fetch("/koleksi/delete-selected", {
                    method: "POST",

                    headers: {
                        "Content-Type": "application/json",

                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]',
                        ).content,
                    },

                    body: JSON.stringify({
                        BukuID: bukuIDs,
                    }),
                });

                const result = await response.json();

                if (result.success) {
                    // hapus card dari tampilan
                    checkedBooks.forEach((check) => {
                        const bukuID = check.value;

                        updateKoleksiUI(bukuID, false);
                    });

                    // keluar dari mode kelola
                    sidebar.classList.remove("manage-mode");
                }
            } catch (error) {
                console.log(error);
            }
        });
    }
    // =========================
    // DETAIL DARI SIDEBAR KOLEKSI
    // =========================

    document.querySelectorAll(".book-slot").forEach((card) => {
        card.addEventListener("click", () => {
            // kalau mode kelola aktif
            if (sidebar.classList.contains("manage-mode")) {
                return;
            }

            const data = card.dataset;
            const bukuID = data.id;
            window.activeBukuID = bukuID;
            window.activeBorrowBook = {
                id: bukuID,
            };

            const borrowTitle = document.getElementById("borrowBookTitle");

            if (borrowTitle) {
                borrowTitle.innerText = data.judul;
            }

            const borrowImage = document.getElementById("borrowBookImage");

            if (borrowImage) {
                borrowImage.src = data.cover;
            }

            // =========================
            // ISI DETAIL
            // =========================

            document.getElementById("detailTitle").innerText = data.judul;

            document.getElementById("dJudul").innerText = data.judul;

            document.getElementById("dPenulis").innerText = data.penulis;

            document.getElementById("dPenerbit").innerText = data.penerbit;

            document.getElementById("dTahun").innerText = data.tahun;

            document.getElementById("dKategori").innerText = data.kategori;

            document.getElementById("dDeskripsi").innerText = data.deskripsi;

            document.getElementById("dStok").innerText = data.stok;

            document.getElementById("detailImage").src = data.cover;

            const ulasanData = JSON.parse(data.ulasan || "[]");

            renderReviews(ulasanData);
            renderRatingSummary(
                card.dataset.ratingSummary
                    ? JSON.parse(card.dataset.ratingSummary)
                    : null,
            );

            // =========================
            // BUTTON KOLEKSI
            // =========================

            const btnAdd = document.getElementById("btnAddCollection");

            btnAdd.dataset.id = bukuID;
            const isKoleksi = card.dataset.koleksi === "true";
            const isDipinjam = card.dataset.dipinjam === "true";

            const peminjamanID = card.dataset.peminjaman;

            updateBorrowButton(isDipinjam, bukuID, peminjamanID);

            updateKoleksiUI(bukuID, isKoleksi);

            // EVENT BUTTON
            btnAdd.onclick = () => {
                const isGuest = document.body.dataset.guest === "true";

                if (isGuest) {
                    requireLogin();
                    return;
                }

                toggleKoleksi(bukuID);
            };

            // tampilkan modal
            detail.classList.add("active");
            lockBody();
            setTimeout(() => {
                document.getElementById("reviewText").focus();
            }, 250);
        });
    });

    // detail
    const detail = document.getElementById("bookDetail");
    const closeDetail = document.getElementById("closeDetail");

    if (detail) {
        document.querySelectorAll(".book-card").forEach((card) => {
            card.addEventListener("click", () => {
                const data = card.dataset;

                const bukuID = data.id;
                window.activeBukuID = bukuID;
                window.activeBorrowBook = {
                    id: bukuID,
                };

                const borrowTitle = document.getElementById("borrowBookTitle");

                if (borrowTitle) {
                    borrowTitle.innerText = data.judul;
                }

                const borrowImage = document.getElementById("borrowBookImage");

                if (borrowImage) {
                    borrowImage.src = data.cover;
                }

                // =========================
                // ISI DETAIL
                // =========================

                document.getElementById("detailTitle").innerText = data.judul;

                document.getElementById("dJudul").innerText = data.judul;

                document.getElementById("dPenulis").innerText = data.penulis;

                document.getElementById("dPenerbit").innerText = data.penerbit;

                document.getElementById("dTahun").innerText = data.tahun;

                document.getElementById("dKategori").innerText = data.kategori;

                document.getElementById("dDeskripsi").innerText =
                    data.deskripsi;

                document.getElementById("dStok").innerText = data.stok;

                document.getElementById("detailImage").src = data.cover;

                const ulasanData = JSON.parse(data.ulasan || "[]");

                renderReviews(ulasanData);
                renderRatingSummary(
                    card.dataset.ratingSummary
                        ? JSON.parse(card.dataset.ratingSummary)
                        : null,
                );

                // =========================
                // BUTTON KOLEKSI
                // =========================

                const btnAdd = document.getElementById("btnAddCollection");

                btnAdd.dataset.id = bukuID;
                const isKoleksi = card.dataset.koleksi === "true";
                const isDipinjam = card.dataset.dipinjam === "true";

                const peminjamanID = card.dataset.peminjaman;

                updateBorrowButton(isDipinjam, bukuID, peminjamanID);

                updateKoleksiUI(bukuID, isKoleksi);

                // RESET EVENT BUTTON
                btnAdd.onclick = () => {
                    const isGuest = document.body.dataset.guest === "true";

                    if (isGuest) {
                        requireLogin();
                        return;
                    }

                    toggleKoleksi(bukuID);
                };
                // tampilkan modal
                detail.classList.add("active");
                lockBody();
                setTimeout(() => {
                    document.getElementById("reviewText").focus();
                }, 250);
            });
        });
    }

    if (closeDetail && detail) {
        closeDetail.addEventListener("click", () => {
            detail.classList.remove("active");
            unlockBody();
            resetReviewInput();
        });
    }

    // Rating dan Ulasan
    const stars = document.querySelectorAll(".star");
    const btnSend = document.getElementById("btnSend");

    function resetReviewInput() {
        currentRating = 0;

        // reset star
        stars.forEach((s) => {
            s.src = "Flaticon/star.png";
        });

        // reset textarea
        document.getElementById("reviewText").value = "";

        // reset tombol
        btnSend.disabled = true;
        btnSend.classList.remove("active");

        // reset icon
        btnSend.innerHTML = `
        <img src="/Flaticon/send.png" alt="send">
    `;
    }

    window.activeBukuID = null;
    let currentRating = 0;
    let hoverRating = 0;

    function updateStarsDisplay(rating) {
        stars.forEach((s, index) => {
            if (index < rating) {
                s.src = "Flaticon/star (1).png";
            } else {
                s.src = "Flaticon/star.png";
            }
        });
    }

    stars.forEach((star) => {
        // HOVER
        star.addEventListener("mouseenter", () => {
            hoverRating = Number(star.dataset.value);

            updateStarsDisplay(hoverRating);
        });

        // CLICK
        star.addEventListener("click", () => {
            currentRating = Number(star.dataset.value);

            updateStarsDisplay(currentRating);

            if (currentRating >= 1) {
                btnSend.disabled = false;
                btnSend.classList.add("active");
            }
        });
    });

    // KELUAR DARI AREA STAR
    document
        .querySelector(".star-rating")
        .addEventListener("mouseleave", () => {
            updateStarsDisplay(currentRating);
        });

    const reviewInput = document.getElementById("reviewText");

    // ENTER untuk kirim ulasan
    reviewInput.addEventListener("keydown", (e) => {
        // Enter tanpa Shift
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();

            if (!btnSend.disabled) {
                btnSend.click();
            }
        }
    });

    btnSend.addEventListener("click", async () => {
        const reviewText = reviewInput.value;

        if (currentRating < 1) return;

        // loading state
        btnSend.disabled = true;

        btnSend.innerHTML = `...`;

        try {
            const response = await fetch("/ulasan/store", {
                method: "POST",

                headers: {
                    "Content-Type": "application/json",

                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content,
                },

                body: JSON.stringify({
                    BukuID: activeBukuID,
                    Rating: currentRating,
                    Ulasan: reviewText,
                }),
            });

            const result = await response.json();

            if (result.success) {
                const reviewsContainer =
                    document.getElementById("reviewsContainer");

                const emptyReview =
                    reviewsContainer.querySelector(".empty-review");

                if (emptyReview) {
                    emptyReview.remove();
                }

                const reviewHTML = `
    <div class="review-item">

        <div class="review-header">

            <img
                src="${result.data.foto}"
                class="review-avatar"
            >

            <div class="review-user">
                <h4>${result.data.nama}</h4>
                <span>@${result.data.username}</span>
            </div>

            <div class="review-time">
                ${result.data.created_at}
            </div>

        </div>

        <div class="review-stars">
            ${Array(result.data.rating)
                .fill(
                    `
                    <img
                        src="/Flaticon/star (1).png"
                        class="review-star-icon"
                    >
                `,
                )
                .join("")}
        </div>

        <p class="review-text">
            ${result.data.ulasan ?? ""}
        </p>

    </div>
`;

                reviewsContainer.insertAdjacentHTML("afterbegin", reviewHTML);

                // update dataset ulasan semua card dengan id sama
                document
                    .querySelectorAll(`[data-id="${activeBukuID}"]`)
                    .forEach((el) => {
                        const oldReviews = JSON.parse(
                            el.dataset.ulasan || "[]",
                        );

                        oldReviews.unshift(result.data);

                        el.dataset.ulasan = JSON.stringify(oldReviews);

                        updateAverageRating(oldReviews);
                    });

                // reset textarea
                document.getElementById("reviewText").value = "";

                // reset rating
                currentRating = 0;

                stars.forEach((s) => {
                    s.src = "Flaticon/star.png";
                });

                btnSend.disabled = true;
                btnSend.classList.remove("active");
                resetReviewInput();
            }
        } catch (error) {
            console.log(error);

            btnSend.disabled = false;

            btnSend.innerHTML = `
        <img src="/Flaticon/send.png" alt="send">
    `;
        }
    });
});

// ======================================
// SEARCH + FILTER KATEGORI
// ======================================

document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("searchInput");

    const categoryButtons = document.querySelectorAll(".category-btn");

    const cards = document.querySelectorAll(".book-card");

    // =========================
    // FILTER FUNCTION
    // =========================

    function filterBooks() {
        const keyword = searchInput.value.toLowerCase();

        let visibleCount = 0;

        cards.forEach((card) => {
            const judul = card.dataset.judul.toLowerCase();

            const penulis = card.dataset.penulis.toLowerCase();

            const kategori = card.dataset.kategori.toLowerCase();

            const matchSearch =
                judul.includes(keyword) || penulis.includes(keyword);

            const matchCategory =
                activeCategories.includes("Semua") ||
                activeCategories.some((cat) =>
                    kategori.includes(cat.toLowerCase()),
                );

            if (matchSearch && matchCategory) {
                card.style.display = "block";

                visibleCount++;
            } else {
                card.style.display = "none";
            }
        });

        const emptySearch = document.getElementById("emptySearch");

        if (visibleCount === 0) {
            emptySearch.style.display = "flex";
        } else {
            emptySearch.style.display = "none";
        }
    }

    // =========================
    // SEARCH INPUT
    // =========================

    searchInput.addEventListener("input", () => {
        filterBooks();
    });

    // ======================================
    // MULTI CATEGORY FILTER
    // ======================================

    let activeCategories = ["Semua"];

    categoryButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
            const category = btn.dataset.category;

            // =========================
            // JIKA TEKAN "SEMUA"
            // =========================

            if (category === "Semua") {
                activeCategories = ["Semua"];

                categoryButtons.forEach((b) => {
                    if (b.dataset.category === "Semua") {
                        b.classList.add("active");
                    } else {
                        b.classList.remove("active");
                    }
                });
            }

            // =========================
            // JIKA KATEGORI BIASA
            // =========================
            else {
                // matikan tombol semua
                activeCategories = activeCategories.filter(
                    (c) => c !== "Semua",
                );

                document
                    .querySelector('.category-btn[data-category="Semua"]')
                    .classList.remove("active");

                // toggle kategori
                if (activeCategories.includes(category)) {
                    activeCategories = activeCategories.filter(
                        (c) => c !== category,
                    );

                    btn.classList.remove("active");
                } else {
                    activeCategories.push(category);

                    btn.classList.add("active");
                }

                // kalau semua kategori dimatikan
                if (activeCategories.length === 0) {
                    activeCategories = ["Semua"];

                    document
                        .querySelector('.category-btn[data-category="Semua"]')
                        .classList.add("active");
                }
            }

            filterBooks();
        });
    });
});
