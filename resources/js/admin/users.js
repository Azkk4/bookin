document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("userSearchInput");
    const emptySearch = document.getElementById("emptyUserSearch");

    const suggestionBox = document.getElementById("searchSuggestions");

    let debounce;
    let cropper = null;
    let usernameExists = false;
    let emailExists = false;

    const menuButtons = document.querySelectorAll(".btn-menu");

    const addUsername = document.getElementById("addUsername");
    const addNama = document.getElementById("addNama");
    const addEmail = document.getElementById("addEmail");
    const addAlamat = document.getElementById("addAlamat");
    const addPassword = document.getElementById("addPassword");
    const addPasswordConfirm = document.getElementById("addPasswordConfirm");
    const usernameError = document.getElementById("usernameError");
    const namaError = document.getElementById("namaError");
    const emailError = document.getElementById("emailError");
    const passwordError = document.getElementById("passwordError");
    const passwordConfirmError = document.getElementById(
        "passwordConfirmError",
    );

    function renderSuggestions(users) {
        suggestionBox.innerHTML = "";

        if (!users.length) {
            suggestionBox.classList.remove("active");

            return;
        }

        users.forEach((user) => {
            const photo = user.Foto
                ? `/storage/user/${user.Foto}`
                : `/images/default-user.png`;

            suggestionBox.innerHTML += `
            <div
                class="suggestion-item"
                data-username="${user.Username}"
            >

                <div class="suggestion-photo">
                    <img src="${photo}">
                </div>

                <div class="suggestion-info">

                    <div class="suggestion-name">
                        ${user.NamaLengkap}
                    </div>

                    <div class="suggestion-email">
                        ${user.Email}
                    </div>

                </div>

            </div>
        `;
        });

        suggestionBox.classList.add("active");
    }

    searchInput.addEventListener("input", () => {
        clearTimeout(debounce);

        const keyword = searchInput.value.trim().toLowerCase();

        // Jika kosong
        if (keyword.length === 0) {
            suggestionBox.classList.remove("active");
            suggestionBox.innerHTML = "";

            userRows.forEach((row) => {
                row.style.display = "flex";
            });

            emptySearch.classList.remove("active");

            updateRowNumbers();

            return;
        }

        // Suggestion AJAX
        if (keyword.length >= 2) {
            debounce = setTimeout(async () => {
                try {
                    const response = await fetch(
                        `/admin/users/suggestions?q=${keyword}`,
                    );

                    const users = await response.json();

                    renderSuggestions(users);
                } catch (err) {
                    console.error(err);
                }
            }, 250);
        }

        let visibleCount = 0;

        userRows.forEach((row) => {
            const text = row.textContent.toLowerCase();

            if (text.includes(keyword)) {
                row.style.display = "flex";
                visibleCount++;
            } else {
                row.style.display = "none";
            }
        });

        if (visibleCount === 0) {
            emptySearch.classList.add("active");
        } else {
            emptySearch.classList.remove("active");
        }

        updateRowNumbers();
    });

    suggestionBox.addEventListener("click", (e) => {
        const item = e.target.closest(".suggestion-item");

        if (!item) return;

        searchInput.value = item.dataset.username;

        suggestionBox.classList.remove("active");

        suggestionBox.innerHTML = "";
    });

    document.addEventListener("click", (e) => {
        if (!e.target.closest(".search-bar-wrapper")) {
            suggestionBox.classList.remove("active");
        }
    });

    menuButtons.forEach((button) => {
        button.addEventListener("click", (e) => {
            e.stopPropagation();

            const dropdown = button
                .closest(".user-menu-wrapper")
                .querySelector(".user-dropdown");

            document.querySelectorAll(".user-dropdown").forEach((menu) => {
                if (menu !== dropdown) {
                    menu.classList.remove("active");
                }
            });

            dropdown.classList.toggle("active");
        });
    });

    const categoryButtons = document.querySelectorAll(".category-btn");

    const userRows = document.querySelectorAll(".user-row-wrapper");

    let activeRoles = [];

    function filterUsers() {
        userRows.forEach((row) => {
            const role = row
                .querySelector(".user-row")
                .dataset.role.toLowerCase();

            if (activeRoles.length === 0 || activeRoles.includes(role)) {
                row.style.display = "flex";
            } else {
                row.style.display = "none";
            }
        });
    }

    categoryButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const role = button.dataset.role.toLowerCase();

            if (role === "all") {
                activeRoles = [];

                categoryButtons.forEach((btn) =>
                    btn.classList.remove("active"),
                );

                button.classList.add("active");
            } else {
                document
                    .querySelector('[data-role="all"]')
                    .classList.remove("active");

                button.classList.toggle("active");

                activeRoles = Array.from(
                    document.querySelectorAll(
                        '.category-btn.active:not([data-role="all"])',
                    ),
                ).map((btn) => btn.dataset.role.toLowerCase());

                if (activeRoles.length === 0) {
                    document
                        .querySelector('[data-role="all"]')
                        .classList.add("active");
                }
            }

            filterUsers();
            updateRowNumbers();
        });
    });

    document.addEventListener("click", () => {
        document.querySelectorAll(".user-dropdown").forEach((menu) => {
            menu.classList.remove("active");
        });
    });

    // OVERLAY DETAIL

    const overlay = document.getElementById("userDetailOverlay");

    const closeBtn = document.getElementById("closeUserDetail");

    const userCenter = document.getElementById("userDetailCenter");

    const userRight = document.getElementById("userDetailRight");

    const editBtn = document.getElementById("openEditUser");

    const cancelBtn = document.getElementById("cancelEditUser");

    const saveBtn = document.getElementById("saveUserDetail");

    const toggleStatusBtn = document.getElementById("toggleUserStatus");

    let currentData = {};

    /* ====================================
   ACTION CONFIRM MODAL
==================================== */

    const actionModal = document.getElementById("actionConfirmModal");

    const actionText = document.getElementById("actionConfirmText");

    const confirmActionBtn = document.getElementById("confirmActionBtn");

    const cancelActionConfirm = document.getElementById("cancelActionConfirm");

    let confirmCallback = null;

    /* OPEN MODAL */

    function openActionModal(text, mode, callback) {
        actionText.textContent = text;

        confirmActionBtn.textContent = mode.button;

        confirmActionBtn.classList.remove(
            "save-mode",
            "discard-mode",
            "disable-mode",
        );

        confirmActionBtn.classList.add(mode.class);

        confirmCallback = callback;

        actionModal.classList.add("active");
    }

    /* CLOSE */

    function closeActionModal() {
        actionModal.classList.remove("active");
    }

    /* CANCEL */

    cancelActionConfirm.addEventListener("click", closeActionModal);

    /* CLICK OUTSIDE */

    actionModal.addEventListener("click", (e) => {
        if (e.target === actionModal) {
            closeActionModal();
        }
    });

    /* CONFIRM */

    confirmActionBtn.addEventListener("click", () => {
        if (confirmCallback) {
            confirmCallback();
        }

        closeActionModal();
    });

    /* ====================================
       OPEN DETAIL
    ==================================== */

    document.querySelectorAll(".user-main").forEach((row) => {
        row.addEventListener("click", async () => {
            const wrapper = row.closest(".user-row");

            const userId = wrapper.dataset.userId;

            try {
                const response = await fetch(`/admin/users/${userId}`);

                const user = await response.json();

                currentData = user;

                currentData.photo = wrapper.dataset.photo;

                renderView();

                detailUserPhoto.src = currentData.photo;

                overlay.classList.add("active");

                closeEdit();
            } catch (error) {
                console.error(error);
            }
        });
    });

    /* ====================================
       CLOSE
    ==================================== */

    closeBtn.addEventListener("click", () => {
        const isEditing = userCenter.classList.contains("editing");

        /* jika sedang edit */
        if (isEditing) {
            openActionModal(
                "Anda yakin ingin membuang perubahan?",
                {
                    button: "Buang",
                    class: "discard-mode",
                },
                () => {
                    overlay.classList.remove("active");

                    closeEdit();
                },
            );

            return;
        }

        /* normal close */
        overlay.classList.remove("active");

        closeEdit();
    });

    /* ====================================
       RENDER
    ==================================== */

    function renderView() {
        document.getElementById("detailUsernameTitle").textContent =
            currentData.Username;

        document.getElementById("vUsername").textContent = currentData.Username;

        document.getElementById("vNama").textContent = currentData.NamaLengkap;

        document.getElementById("vEmail").textContent = currentData.Email;

        document.getElementById("vRole").textContent = currentData.Role;

        document.getElementById("vStatus").textContent = currentData.Status;

        document.getElementById("vAlamat").textContent = currentData.Alamat;

        document.getElementById("vTanggal").textContent = new Date(
            currentData.created_at,
        ).toLocaleDateString("id-ID");

        document.getElementById("detailRoleLabel").textContent =
            currentData.Role;

        const statusLabel = document.getElementById("detailStatusLabel");

        statusLabel.textContent = currentData.Status;

        statusLabel.classList.remove("status-active", "status-inactive");

        if (currentData.Status === "Aktif") {
            statusLabel.classList.add("status-active");
        } else {
            statusLabel.classList.add("status-inactive");
        }

        document.getElementById("detailUserPhoto").src =
            currentData.photo || "/images/default-user.png";

        toggleStatusBtn.textContent =
            currentData.Status === "Aktif" ? "Nonaktifkan" : "Aktifkan";

        const statusBtn = document.getElementById("toggleUserStatus");

        if (currentData.Status === "Aktif") {
            statusBtn.textContent = "Nonaktifkan";

            statusBtn.classList.remove("active-user");

            statusBtn.classList.add("inactive-user");
        } else {
            statusBtn.textContent = "Aktifkan";

            statusBtn.classList.remove("inactive-user");

            statusBtn.classList.add("active-user");
        }

        promoteAdminBtn.style.display = "block";
        promotePetugasBtn.style.display = "block";
        promotePeminjamBtn.style.display = "block";

        if (currentData.Role === "Admin") {
            promoteAdminBtn.style.display = "none";
        }

        if (currentData.Role === "Petugas") {
            promotePetugasBtn.style.display = "none";
        }

        if (currentData.Role === "Peminjam") {
            promotePeminjamBtn.style.display = "none";
        }
    }

    function updateRowNumbers() {
        const visibleRows = document.querySelectorAll(
            '.user-row-wrapper:not([style*="display: none"])',
        );

        visibleRows.forEach((row, index) => {
            const number = row.querySelector(".row-number");

            if (number) {
                number.textContent = index + 1;
            }
        });
    }

    /* ====================================
       EDIT
    ==================================== */

    function fillInputs() {
        document.getElementById("eUsername").value = currentData.Username;

        document.getElementById("eNama").value = currentData.NamaLengkap;

        document.getElementById("eEmail").value = currentData.Email;

        document.getElementById("eAlamat").value = currentData.Alamat;
    }

    const backBtn = document.getElementById("closeUserDetail");
    const addUserBackBtn = document.getElementById("closeAddUser");

    function openEdit() {
        fillInputs();

        userCenter.classList.add("editing");
        userRight.classList.add("editing");

        backBtn.classList.add("back-btn-hidden");
    }

    function closeEdit() {
        userCenter.classList.remove("editing");
        userRight.classList.remove("editing");

        backBtn.classList.remove("back-btn-hidden");
    }

    editBtn.addEventListener("click", openEdit);

    cancelBtn.addEventListener("click", () => {
        openActionModal(
            "Anda yakin ingin membuang perubahan?",
            {
                button: "Buang",
                class: "discard-mode",
            },
            () => {
                closeEdit();
            },
        );
    });

    saveBtn.addEventListener("click", () => {
        openActionModal(
            "Anda yakin ingin menyimpan data?",
            {
                button: "Simpan",
                class: "save-mode",
            },

            async () => {
                try {
                    const response = await fetch(
                        `/admin/users/${currentData.UserID}`,
                        {
                            method: "PUT",

                            headers: {
                                "Content-Type": "application/json",

                                "X-CSRF-TOKEN": document.querySelector(
                                    'meta[name="csrf-token"]',
                                ).content,
                            },

                            body: JSON.stringify({
                                Username: eUsername.value,

                                NamaLengkap: eNama.value,

                                Email: eEmail.value,

                                Alamat: eAlamat.value,
                            }),
                        },
                    );

                    const result = await response.json();

                    if (result.success) {
                        currentData.Username = eUsername.value;

                        currentData.NamaLengkap = eNama.value;

                        currentData.Email = eEmail.value;

                        renderView();

                        closeEdit();

                        location.reload();
                    }
                } catch (error) {
                    console.error(error);
                }
            },
        );
    });

    /* ====================================
       STATUS
    ==================================== */

    toggleStatusBtn.addEventListener("click", () => {
        const action =
            currentData.Status === "Aktif" ? "nonaktifkan" : "aktifkan";

        const isActive = currentData.Status === "Aktif";

        openActionModal(
            `Anda yakin ingin ${action} user?`,
            {
                button: isActive ? "Nonaktifkan" : "Aktifkan",

                class: isActive ? "disable-mode" : "save-mode",
            },

            async () => {
                try {
                    const response = await fetch(
                        `/admin/users/${currentData.UserID}/status`,
                        {
                            method: "PATCH",

                            headers: {
                                "X-CSRF-TOKEN": document.querySelector(
                                    'meta[name="csrf-token"]',
                                ).content,
                            },
                        },
                    );

                    const result = await response.json();

                    currentData.Status = result.status;

                    renderView();

                    location.reload();
                } catch (error) {
                    console.error(error);
                }
            },
        );
    });

    async function updateRole(role) {
        try {
            const response = await fetch(
                `/admin/users/${currentData.UserID}/role`,
                {
                    method: "PATCH",

                    headers: {
                        "Content-Type": "application/json",

                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]',
                        ).content,
                    },

                    body: JSON.stringify({
                        Role: role,
                    }),
                },
            );

            const result = await response.json();

            if (result.success) {
                currentData.Role = role;

                renderView();

                location.reload();
            }
        } catch (error) {
            console.error(error);
        }
    }

    const promoteAdminBtn = document.getElementById("promoteToAdmin");

    const promotePetugasBtn = document.getElementById("promoteToPetugas");

    const promotePeminjamBtn = document.getElementById("promoteToPeminjam");

    promoteAdminBtn.addEventListener("click", () => {
        openActionModal(
            "Anda yakin ingin menjadikan user sebagai Admin?",
            {
                button: "Jadikan Admin",
                class: "save-mode",
            },
            () => {
                updateRole("Admin");
            },
        );
    });

    promotePetugasBtn.addEventListener("click", () => {
        openActionModal(
            "Anda yakin ingin menjadikan user sebagai Petugas?",
            {
                button: "Jadikan Petugas",
                class: "save-mode",
            },
            () => {
                updateRole("Petugas");
            },
        );
    });

    promotePeminjamBtn.addEventListener("click", () => {
        openActionModal(
            "Anda yakin ingin menjadikan user sebagai Peminjam?",
            {
                button: "Jadikan Peminjam",
                class: "save-mode",
            },
            () => {
                updateRole("Peminjam");
            },
        );
    });

    /* ====================================
   TAMBAH USER
==================================== */

    const addUserOverlay = document.getElementById("addUserOverlay");

    const addUserTitle = document.getElementById("addUserTitle");

    const addAdminBtn = document.querySelector(".btn-add-admin");

    const addPetugasBtn = document.querySelector(".btn-add-petugas");

    let selectedRole = "";

    addAdminBtn.addEventListener("click", () => {
        addUserBackBtn.classList.add("back-btn-hidden");

        selectedRole = "Admin";

        addUserTitle.textContent = "Tambah Admin";

        document.getElementById("newUserRoleLabel").textContent = "Admin";

        addUserOverlay.classList.add("active");
    });

    addPetugasBtn.addEventListener("click", () => {
        addUserBackBtn.classList.add("back-btn-hidden");

        selectedRole = "Petugas";

        addUserTitle.textContent = "Tambah Petugas";

        document.getElementById("newUserRoleLabel").textContent = "Petugas";

        addUserOverlay.classList.add("active");
    });

    const cancelAddUser = document.getElementById("cancelAddUser");

    cancelAddUser.addEventListener("click", () => {
        openActionModal(
            "Anda yakin ingin membatalkan penambahan user?",
            {
                button: "Buang",
                class: "discard-mode",
            },
            () => {
                addUserOverlay.classList.remove("active");

                resetAddUserForm();

                addUserBackBtn.classList.remove("back-btn-hidden");
            },
        );
    });

    const addFoto = document.getElementById("addFoto");

    const previewAddPhoto = document.getElementById("previewAddPhoto");

    addFoto.addEventListener("change", function () {
        const file = this.files[0];

        if (!file) return;

        const reader = new FileReader();

        reader.onload = function (e) {
            previewAddPhoto.src = e.target.result;
        };

        reader.readAsDataURL(file);
    });

    function resetAddUserForm() {
        addUsername.value = "";
        addNama.value = "";
        addEmail.value = "";
        addPassword.value = "";
        addPasswordConfirm.value = "";
        addFoto.value = "";

        selectedRole = "";
    }

    addUserBackBtn.addEventListener("click", () => {
        openActionModal(
            "Anda yakin ingin membatalkan penambahan user?",
            {
                button: "Buang",
                class: "discard-mode",
            },
            () => {
                addUserOverlay.classList.remove("active");

                resetAddUserForm();

                addUserBackBtn.classList.remove("back-btn-hidden");
            },
        );
    });

    const touchedFields = {
        username: false,
        nama: false,
        email: false,
        password: false,
        passwordConfirm: false,
    };

    function validateForm() {
        let valid = true;

        usernameError.textContent = "";
        namaError.textContent = "";
        emailError.textContent = "";
        passwordError.textContent = "";
        passwordConfirmError.textContent = "";

        // wajib isi
        if (
            !addUsername.value.trim() ||
            !addNama.value.trim() ||
            !addEmail.value.trim() ||
            !addPassword.value.trim() ||
            !addPasswordConfirm.value.trim()
        ) {
            valid = false;
        }

        // username
        if (touchedFields.username) {
            if (addUsername.value.trim().length < 3) {
                usernameError.textContent = "Username minimal 3 karakter";
                valid = false;
            } else if (addUsername.value.trim().length > 255) {
                usernameError.textContent = "Username maksimal 255 karakter";
                valid = false;
            } else if (usernameExists) {
                usernameError.textContent = "Username sudah digunakan";
                valid = false;
            }
        }

        // nama
        if (touchedFields.nama) {
            if (addNama.value.trim().length < 3) {
                namaError.textContent = "Nama terlalu pendek";
                valid = false;
            } else if (addNama.value.trim().length > 255) {
                namaError.textContent = "Nama maksimal 255 karakter";
                valid = false;
            }
        }

        // email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (touchedFields.email) {
            if (!emailRegex.test(addEmail.value.trim())) {
                emailError.textContent = "Format email tidak valid";
                valid = false;
            } else if (addEmail.value.trim().length > 255) {
                emailError.textContent = "Email maksimal 255 karakter";
                valid = false;
            } else if (emailExists) {
                emailError.textContent = "Email sudah digunakan";
                valid = false;
            }
        }

        // password
        if (touchedFields.password) {
            if (addPassword.value.length < 8) {
                passwordError.textContent = "Password minimal 8 karakter";
                valid = false;
            } else if (addPassword.value.length > 255) {
                passwordError.textContent = "Password maksimal 255 karakter";
                valid = false;
            }
        }

        // konfirmasi password
        if (touchedFields.passwordConfirm) {
            if (addPassword.value !== addPasswordConfirm.value) {
                passwordConfirmError.textContent = "Password tidak cocok";
                valid = false;
            }
        }

        return valid;
    }

    function updateButtonState() {
        const formValid = validateForm();

        saveNewUser.disabled = !formValid;
    }

    async function checkDuplicateFields() {
        const username = addUsername.value.trim();
        const email = addEmail.value.trim();

        try {
            const response = await fetch(
                `/admin/check-user?username=${encodeURIComponent(username)}&email=${encodeURIComponent(email)}`,
            );

            const data = await response.json();

            usernameExists = data.usernameExists;
            emailExists = data.emailExists;

            if (usernameExists) {
                usernameError.textContent = "Username sudah digunakan";
            } else if (
                usernameError.textContent === "Username sudah digunakan"
            ) {
                usernameError.textContent = "";
            }

            if (emailExists) {
                emailError.textContent = "Email sudah digunakan";
            } else if (emailError.textContent === "Email sudah digunakan") {
                emailError.textContent = "";
            }

            validateForm();
        } catch (err) {
            console.error(err);
        }
    }

    addUsername.addEventListener("input", async () => {
        touchedFields.username = true;

        const username = addUsername.value.trim();

        if (username.length < 3) {
            usernameExists = false;

            validateForm();
            updateButtonState();

            return;
        }

        try {
            const response = await fetch(
                `/admin/check-user?username=${encodeURIComponent(username)}`,
            );

            const result = await response.json();

            usernameExists = result.usernameExists;
        } catch (err) {
            console.error(err);
        }

        validateForm();
        updateButtonState();
    });

    addNama.addEventListener("input", () => {
        touchedFields.nama = true;
        validateForm();
        updateButtonState();
    });

    addEmail.addEventListener("input", async () => {
        touchedFields.email = true;

        const email = addEmail.value.trim();

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailRegex.test(email)) {
            emailExists = false;

            validateForm();
            updateButtonState();

            return;
        }

        try {
            const response = await fetch(
                `/admin/check-user?email=${encodeURIComponent(email)}`,
            );

            const result = await response.json();

            emailExists = result.emailExists;
        } catch (err) {
            console.error(err);
        }

        validateForm();
        updateButtonState();
    });

    addPassword.addEventListener("input", () => {
        touchedFields.password = true;
        validateForm();
        updateButtonState();
    });

    addPasswordConfirm.addEventListener("input", () => {
        touchedFields.passwordConfirm = true;
        validateForm();
        updateButtonState();
    });

    addPassword.addEventListener("input", () => {
        const password = addPassword.value.trim();

        const fill = document.getElementById("strengthFill");
        const text = document.getElementById("strengthText");

        let score = 0;

        // Panjang password
        if (password.length >= 8) score++;
        if (password.length >= 10) score++;
        if (password.length >= 12) score++;

        // Bonus jika ada angka atau simbol
        if (/[0-9]/.test(password) || /[^A-Za-z0-9]/.test(password)) {
            score++;
        }

        if (score <= 1) {
            fill.style.width = "25%";
            fill.style.background = "#ef4444";

            text.textContent = "Password Lemah";
        } else if (score === 2) {
            fill.style.width = "50%";
            fill.style.background = "#f59e0b";

            text.textContent = "Password Sedang";
        } else if (score === 3) {
            fill.style.width = "75%";
            fill.style.background = "#22c55e";

            text.textContent = "Password Kuat";
        } else {
            fill.style.width = "100%";
            fill.style.background = "#15803d";

            text.textContent = "Password Sangat Kuat";
        }
    });

    const saveNewUser = document.getElementById("saveNewUser");

    saveNewUser.disabled = true;

    saveNewUser.addEventListener("click", () => {
        if (!validateForm()) {
            return;
        }

        openActionModal(
            "Anda yakin ingin menambahkan user?",
            {
                button: "Simpan",
                class: "save-mode",
            },

            async () => {
                const formData = new FormData();

                formData.append("Username", addUsername.value);

                formData.append("NamaLengkap", addNama.value);

                formData.append("Email", addEmail.value);

                formData.append("Password", addPassword.value);

                formData.append("Role", selectedRole);

                formData.append("Alamat", addAlamat.value);

                if (addFoto.files[0]) {
                    formData.append("Foto", addFoto.files[0]);
                }

                if (!selectedRole) {
                    alert("Role belum dipilih");
                    return;
                }

                if (addPassword.value !== addPasswordConfirm.value) {
                    alert("Konfirmasi password tidak cocok");
                    return;
                }

                if (addPassword.value.length < 8) {
                    alert("Password minimal 8 karakter");
                    return;
                }

                const response = await fetch("/admin/users/store", {
                    method: "POST",

                    headers: {
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]',
                        ).content,
                    },

                    body: formData,
                });

                const result = await response.json();

                if (!response.ok) {
                    if (result.errors) {
                        if (result.errors.Username) {
                            usernameError.textContent =
                                result.errors.Username[0];
                        }

                        if (result.errors.Email) {
                            emailError.textContent = result.errors.Email[0];
                        }
                    }

                    return;
                }

                if (result.success) {
                    addUserBackBtn.classList.remove("back-btn-hidden");

                    location.reload();
                }
            },
        );
    });

    addUsername.addEventListener("input", () => {
        addUsername.value = addUsername.value
            .replace(/[^a-zA-Z0-9_]/g, "")
            .slice(0, 255);
    });

    addNama.addEventListener("input", () => {
        addNama.value = addNama.value
            .replace(/[^a-zA-Z\s]/g, "")
            .replace(/\s+/g, " ")
            .trimStart()
            .slice(0, 255);
    });

    document.querySelectorAll(".toggle-password").forEach((icon) => {
        icon.addEventListener("click", () => {
            const target = document.getElementById(icon.dataset.target);

            if (target.type === "password") {
                target.type = "text";

                icon.src = "/Flaticon/show.png";
            } else {
                target.type = "password";

                icon.src = "/Flaticon/eye.png";
            }
        });
    });

    /* ====================================
   PHOTO UPLOAD
==================================== */

    const userPhotoInput = document.getElementById("userPhotoInput");

    const detailUserPhoto = document.getElementById("detailUserPhoto");

    const photoUploadBox = document.getElementById("photoUploadBox");

    const cropOverlay = document.getElementById("cropOverlay");

    const cropImage = document.getElementById("cropImage");

    const applyCrop = document.getElementById("applyCrop");

    const cancelCrop = document.getElementById("cancelCrop");

    /* FILE INPUT */

    if (userPhotoInput) {
        userPhotoInput.addEventListener("change", function () {
            const file = this.files[0];

            if (!file) return;

            const reader = new FileReader();

            reader.onload = function (e) {
                cropImage.src = e.target.result;

                cropOverlay.classList.add("active");

                if (cropper) {
                    cropper.destroy();
                }

                cropper = new Cropper(cropImage, {
                    aspectRatio: 1,

                    viewMode: 1,

                    dragMode: "move",

                    autoCropArea: 1,

                    responsive: true,
                });
            };

            reader.readAsDataURL(file);
        });
    }

    /* DRAG DROP */
    function openCropper(file) {
        const reader = new FileReader();

        reader.onload = function (e) {
            cropImage.src = e.target.result;

            cropOverlay.classList.add("active");

            if (cropper) {
                cropper.destroy();
            }

            cropper = new Cropper(cropImage, {
                aspectRatio: 1,
                viewMode: 1,
                autoCropArea: 1,
            });
        };

        reader.readAsDataURL(file);
    }

    if (photoUploadBox) {
        photoUploadBox.addEventListener("dragover", (e) => {
            e.preventDefault();

            photoUploadBox.classList.add("dragover");
        });

        photoUploadBox.addEventListener("dragleave", () => {
            photoUploadBox.classList.remove("dragover");
        });

        photoUploadBox.addEventListener("drop", (e) => {
            e.preventDefault();

            photoUploadBox.classList.remove("dragover");

            const file = e.dataTransfer.files[0];

            if (!file) return;

            openCropper(file);
        });
    }

    applyCrop.addEventListener("click", async () => {
        const canvas = cropper.getCroppedCanvas({
            width: 400,
            height: 400,
        });

        detailUserPhoto.src = canvas.toDataURL("image/png");

        canvas.toBlob(async (blob) => {
            const formData = new FormData();

            formData.append("photo", blob, "profile.png");

            try {
                const response = await fetch(
                    `/admin/users/${currentData.UserID}/photo`,
                    {
                        method: "POST",

                        headers: {
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]',
                            ).content,
                        },

                        body: formData,
                    },
                );

                const result = await response.json();

                if (result.success) {
                    currentData.photo = result.photo;

                    detailUserPhoto.src = result.photo;

                    const currentRow = document.querySelector(
                        `.user-row[data-user-id="${currentData.UserID}"]`,
                    );

                    if (currentRow) {
                        currentRow.dataset.photo = result.photo;

                        const rowImage =
                            currentRow.querySelector(".user-photo img");

                        if (rowImage) {
                            rowImage.src = result.photo;
                        }
                    }
                }
            } catch (error) {
                console.error(error);
            }
        });

        cropOverlay.classList.remove("active");

        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    });

    cancelCrop.addEventListener("click", () => {
        cropOverlay.classList.remove("active");

        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    });
});
