<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Kelola User</title>

    @vite([
        'resources/css/admin/users.css',
        'resources/css/admin/generate-laporan.css',
        'resources/css/admin/preview-laporan.css',
        'resources/css/logout.css',
        'resources/js/admin/users.js',
        'resources/js/admin/generate-laporan.js',
        'resources/js/logout.js',
    ])

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css"
/>
</head>

<body>
  @include('components.modals.logout')
  @include('components.modals.action-confirm')
  @include('components.modals.generate-laporan')
  @include('components.modals.preview-laporan')

<header class="header">

    <div class="left-header">

        <div class="logo-box">
        </div>

        <div class="logo-icon">
            <img src="{{ asset('images/Trinity Logo Cropped.png') }}">
        </div>

    </div>

    
        <form
        method="GET"
        action="{{ route('admin.users') }}"
        class="search-wrapper"
        >
            <a href="{{ url('/admin/users') }}" class="home-icon">
                <img src="{{ asset('Flaticon/home.png') }}">
            </a>

            <div class="search-bar-wrapper">

                <div class="search-bar">

                    <img
                        src="{{ asset('Flaticon/search.png') }}"
                        class="search-icon"
                    >

                    <input
                        type="text"
                        name="search"
                        id="userSearchInput"
                        value="{{ request('search') }}"
                        placeholder="Ada anggota yang ingin dicari?"
                        autocomplete="off"
                    >

                </div>

                <div
                    class="search-suggestions"
                    id="searchSuggestions"
                ></div>

            </div>

            <button
                type="submit"
                class="btn-search"
            >
                Cari
            </button>

        </form>

    <div class="right-header">

        <a href="{{ route('admin.activity') }}" class="icon-btn">
            <img src="{{ asset('Flaticon/history (1).png') }}">
        </a>

        <button class="icon-btn logout" id="openLogout">
            <img src="{{ asset('Flaticon/turn-off.png') }}">
        </button>

    </div>

</header>

<main class="admin-container">

    <!-- TOP -->
    <div class="top-bar">

        <div class="categories">

            <button class="category-btn active" data-role="all">
                Semua
            </button>

            <button class="category-btn" data-role="Admin">
                Admin
            </button>

            <button class="category-btn" data-role="Petugas">
                Petugas
            </button>

            <button class="category-btn" data-role="Peminjam">
                Peminjam
            </button>

        </div>

        <div class="admin-actions">

            <button class="btn-report">
                <img src="{{ asset('Flaticon/plus.png') }}">
                <span>Laporan</span>
            </button>

            <button class="btn-add-admin" data-role="Admin">
                <img src="{{ asset('Flaticon/plus.png') }}">
                <span>Admin</span>
            </button>

            <button class="btn-add-petugas" data-role="Petugas">
                <img src="{{ asset('Flaticon/plus.png') }}">
                <span>Petugas</span>
            </button>

            <a href="{{ url('/admin/dashboard') }}" class="btn-books">

                <img src="{{ asset('Flaticon/book.png') }}">

                <span>Kelola Buku</span>

            </a>

        </div>

    </div>

    <!-- STATS -->
    <div class="stats">

        <div class="stat-card">
            <h4>Total Anggota</h4>
            <h2>{{ $totalAnggota }}</h2>
        </div>

        <div class="stat-card">
            <h4>Anggota Aktif</h4>
            <h2>{{ $anggotaAktif }}</h2>
        </div>

        <div class="stat-card">
            <h4>Total Peminjam</h4>
            <h2>{{ $totalPeminjam }}</h2>
        </div>

        <div class="stat-card">
            <h4>Total Petugas</h4>
            <h2>{{ $totalPetugas }}</h2>
        </div>

        <div class="stat-card">
            <h4>Total Admin</h4>
            <h2>{{ $totalAdmin }}</h2>
        </div>

    </div>

    <!-- USERS -->

    <div class="user-table-header">

    <div>Foto</div>

    <div>Username</div>

    <div>Nama Lengkap</div>

    <div>Email</div>

    <div>Role</div>

    <div>Status</div>

    <div>Tanggal Daftar</div>

</div>

    @foreach($users as $index => $user)

        <div class="user-row-wrapper">

            <div class="row-number">
                {{ $users->firstItem() + $index }}
            </div>

            <div class="user-row role-{{ strtolower($user->Role) }}"
            data-role="{{ strtolower($user->Role) }}"
            data-user-id="{{ $user->UserID }}"
            data-photo="{{ $user->Foto
            ? asset('storage/user/' . $user->Foto)
            : asset('images/default-user.png') }}">

    <div class="user-main">

        <div class="user-photo">

            @if($user->Foto)
                <img src="{{ asset('storage/user/' . $user->Foto) }}">
            @else
                <img src="{{ asset('images/default-user.png') }}">
            @endif

        </div>

        <div
            class="username"
            title="{{ $user->Username }}"
        >
            {{ $user->Username }}
        </div>

        <div
            class="user-name"
            title="{{ $user->NamaLengkap }}"
        >
            {{ $user->NamaLengkap }}
        </div>

        <div
            class="user-email"
            title="{{ $user->Email }}"
        >
            {{ $user->Email }}
        </div>

        <div class="user-role">
            {{ $user->Role }}
        </div>

        <div class="user-status {{ $user->Status == 'Aktif' ? 'active' : 'inactive' }}">
            {{ $user->Status }}
        </div>

        <div class="user-date">
            {{ $user->created_at->format('d F Y') }}
        </div>

    </div>

</div>

        </div>

    @endforeach

    <!-- FOOTER -->
    <div class="bottom-bar">

        <div class="pagination">
            @if ($users->hasPages())

<div class="custom-pagination">

    @if ($users->onFirstPage())
        <span class="disabled">&lt;</span>
    @else
        <a href="{{ $users->previousPageUrl() }}">&lt;</a>
    @endif

    @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)

        @if ($page == $users->currentPage())
            <span class="active">{{ $page }}</span>
        @else
            <a href="{{ $url }}">{{ $page }}</a>
        @endif

    @endforeach

    @if ($users->hasMorePages())
        <a href="{{ $users->nextPageUrl() }}">&gt;</a>
    @else
        <span class="disabled">&gt;</span>
    @endif

</div>

@endif
        </div>

    </div>

</main>

<!-- USER DETAIL OVERLAY -->
<div class="user-detail-overlay" id="userDetailOverlay">

    <div class="user-detail-container">

        <!-- HEADER -->
        <div class="user-detail-header">

            <button class="user-back-btn" id="closeUserDetail">
                <img src="{{ asset('Flaticon/back.png') }}">
            </button>

        </div>

        <!-- LEFT -->
        <div class="user-detail-left">

            <h2>Profil <span id="detailUsernameTitle">Kaaza</span>,</h2>

            <label class="user-photo-box" id="photoUploadBox">

              <img
              src="{{ asset('images/default-user.png') }}"
              id="detailUserPhoto"
              alt="Foto Pengguna"
              >
              
              <div class="user-photo-overlay">
                Ganti Foto
              </div>
              
              <input
                  type="file"
                  id="userPhotoInput"
                  hidden
                  accept="image/*"
              >

            </label>

        </div>

        <!-- CENTER -->
        <div class="user-detail-center" id="userDetailCenter">

            <div class="user-detail-field editable-field">
                <label>Username</label>

                <span class="user-separator">:</span>

                <span class="view-user-value" id="vUsername"></span>

                <input type="text" class="edit-user-input" id="eUsername">
            </div>

            <div class="user-detail-field editable-field">
                <label>Nama Lengkap</label>

                <span class="user-separator">:</span>

                <span class="view-user-value" id="vNama"></span>

                <input type="text" class="edit-user-input" id="eNama">
            </div>

            <div class="user-detail-field editable-field">
                <label>Email</label>

                <span class="user-separator">:</span>

                <span class="view-user-value" id="vEmail"></span>

                <input type="email" class="edit-user-input" id="eEmail">
            </div>

            <div class="user-detail-field">
                <label>Role</label>

                <span class="user-separator">:</span>

                <span class="view-user-value" id="vRole"></span>
            </div>

            <div class="user-detail-field">
                <label>Status</label>

                <span class="user-separator">:</span>

                <span class="view-user-value" id="vStatus"></span>

            </div>

            <div class="user-detail-field">
                <label>Tanggal Daftar</label>

                <span class="user-separator">:</span>

                <span class="view-user-value" id="vTanggal"></span>

            </div>

            <div class="user-detail-field editable-field">
                <label>Alamat</label>

                <span class="user-separator">:</span>

                <span class="view-user-value" id="vAlamat"></span>

                <textarea
                    class="edit-user-input"
                    id="eAlamat"
                    rows="4"
                ></textarea>

            </div>

        </div>

        <!-- RIGHT -->
        <div class="user-detail-right" id="userDetailRight">

            <!-- BOX STATUS -->
            <div class="user-status-box">

                <p>
                    Role :
                    <span id="detailRoleLabel">Peminjam</span>
                </p>

                <p>
                    Status :
                    <span id="detailStatusLabel">Aktif</span>
                </p>

                <!-- VIEW MODE -->
                <div class="user-action-view">

                    <button id="toggleUserStatus" class="btn-disable-user">
                        Nonaktifkan
                    </button>

                    <button id="openEditUser" class="btn-edit-user">
                        Edit
                    </button>

                </div>

                <!-- EDIT MODE -->
                <div class="user-action-edit">

                    <button class="btn-save-user" id="saveUserDetail">
                        Simpan
                    </button>

                    <button class="btn-cancel-user" id="cancelEditUser">
                        Batal
                    </button>

                </div>

            </div>

            <!-- BOX ROLE -->
            <div class="user-role-box">

                <h4>Ubah Role</h4>

                <button id="promoteToAdmin">
                    Jadikan Admin
                </button>

                <button id="promoteToPetugas">
                    Jadikan Petugas
                </button>

                <button id="promoteToPeminjam">
                    Jadikan Peminjam
                </button>

            </div>

        </div>

    </div>

</div>

<div class="user-detail-overlay" id="addUserOverlay">

    <div class="user-detail-container">

        <!-- HEADER -->
        <div class="user-detail-header">

            <button
                class="user-back-btn"
                id="closeAddUser"
            >
                <img src="{{ asset('Flaticon/back.png') }}">
            </button>

        </div>

        <!-- LEFT -->
        <div class="user-detail-left">

            <h2 id="addUserTitle">
                Tambah Admin
            </h2>

            <label
                class="user-photo-box"
                for="addFoto"
            >

                <img
                    src="{{ asset('images/default-user.png') }}"
                    id="previewAddPhoto"
                >

                <div class="user-photo-overlay">
                    Pilih Foto
                </div>

            </label>

            <input
                type="file"
                id="addFoto"
                hidden
                accept="image/*"
            >

        </div>

        <!-- CENTER -->
        <div class="user-detail-center">

            <div class="user-detail-field">

                <label>Username</label>

                <span>:</span>

                <div class="field-content">

                    <input
                        type="text"
                        id="addUsername"
                        class="edit-user-input"
                        maxlength="255"
                    >

                    <div class="input-error" id="usernameError"></div>

                </div>

            </div>

            <div class="user-detail-field">

                <label>Nama Lengkap</label>

                <span>:</span>

                <div class="field-content">

                    <input
                        type="text"
                        id="addNama"
                        class="edit-user-input"
                        style="display:block"
                        maxlength="255"

                >

                    <div class="input-error" id="namaError"></div>

                </div>

            </div>

            <div class="user-detail-field">

                <label>Email</label>

                <span>:</span>

                <div class="field-content">

                    <input
                        type="email"
                        id="addEmail"
                        class="edit-user-input"
                        maxlength="255"
                >

                    <div class="input-error" id="emailError"></div>

                </div>

            </div>

            <div class="user-detail-field password-field">

                <label>Password</label>

                <span>:</span>

                <div class="field-content">

                    <div class="password-wrapper">

                        <input
                            type="password"
                            id="addPassword"
                            class="edit-user-input"
                            maxlength="255"
                            autocomplete="new-password"
                        >

                        <img
                            src="{{ asset('Flaticon/eye.png') }}"
                            class="toggle-password"
                            data-target="addPassword"
                        >

                    </div>

                    <div class="password-strength">

                        <div class="strength-bar">
                            <span id="strengthFill"></span>
                        </div>

                        <small id="strengthText">
                            Password belum diisi
                        </small>

                    </div>

                    <div
                        class="input-error"
                        id="passwordError"
                    ></div>

                </div>

            </div>

            <div class="user-detail-field">

                <label>Konfirmasi Password</label>

                <span>:</span>

                <div class="field-content">

                    <div class="password-wrapper">

                        <input
                            type="password"
                            id="addPasswordConfirm"
                            class="edit-user-input"
                            maxlength="255"
                            autocomplete="new-password"
                        >

                        <img
                            src="{{ asset('Flaticon/eye.png') }}"
                            class="toggle-password"
                            data-target="addPasswordConfirm"
                        >

                    </div>

                    <div
                        class="input-error"
                        id="passwordConfirmError"
                    ></div>

                </div>

            </div>

            <div class="user-detail-field">

                <label>Alamat</label>

                <span>:</span>

                <div class="field-content">

                    <textarea
                        id="addAlamat"
                        class="edit-user-input"
                        rows="4"
                    ></textarea>

                    <div class="input-error" id="emailError"></div>

                </div>

            </div>

        </div>

        <!-- RIGHT -->
        <div class="user-detail-right">

            <div class="user-status-box">

                <p>
                    Role :
                    <span id="newUserRoleLabel">
                        Admin
                    </span>
                </p>

                <div class="user-action-edit">

                    <button
                        class="btn-save-user"
                        id="saveNewUser"
                    >
                        Simpan
                    </button>

                    <button
                        class="btn-cancel-user"
                        id="cancelAddUser"
                    >
                        Batal
                    </button>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

<div class="crop-overlay" id="cropOverlay">

    <div class="crop-box">

        <img id="cropImage">

        <div class="crop-actions">

            <button id="cancelCrop">
                Batal
            </button>

            <button id="applyCrop">
                Gunakan
            </button>

        </div>

    </div>

</div>

</body>
</html>