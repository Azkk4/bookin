document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("pengembalianModal");

    const openBtn = document.getElementById("openPengembalian");

    const closeBtn = document.getElementById("closePengembalian");

    // buka
    if (openBtn) {
        openBtn.addEventListener("click", () => {
            modal.classList.add("active");
            lockBody();
        });
    }

    // close
    if (closeBtn) {
        closeBtn.addEventListener("click", () => {
            modal.classList.remove("active");
            unlockBody();
        });
    }

    // tombol pengembalian
    document.querySelectorAll(".btn-kembalikan").forEach((btn) => {
        btn.addEventListener("click", () => {
            const peminjamanID = btn.dataset.id;

            openActionModal(
                "Anda yakin ingin mengembalikan buku ini?",
                {
                    button: "Kembalikan",
                    class: "discard-mode",
                },

                async () => {
                    try {
                        btn.disabled = true;

                        btn.innerText = "...";

                        const response = await fetch(
                            `/pengembalian/${peminjamanID}`,
                            {
                                method: "POST",

                                headers: {
                                    "Content-Type": "application/json",

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

                        // notif sukses
                        showBorrowToast(result.message);

                        // hapus card
                        btn.closest(".pengembalian-card").remove();

                        // delay agar toast sempat terlihat
                        setTimeout(() => {
                            window.location.reload();
                        }, 1200);
                    } catch (error) {
                        console.log(error);

                        alert("Terjadi kesalahan");
                    } finally {
                        btn.disabled = false;

                        btn.innerText = "Kembalikan Buku";
                    }
                },
            );
        });
    });
});
