<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Dashboard Admin</title>

    @vite([
      'resources/css/admin/dashboard.css',
      'resources/css/admin/hapus-buku.css',
      'resources/css/admin/generate-laporan.css',
      'resources/css/admin/preview-laporan.css',

      'resources/css/logout.css',

      'resources/js/logout.js',
      'resources/js/admin/dashboard.js',
      'resources/js/admin/generate-laporan.js'
    ])


    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  </head>

  <body>
  @include('components.modals.logout')
  @include('components.modals.hapus-buku')
  @include('components.modals.generate-laporan')
  @include('components.modals.preview-laporan')

    {{-- HEADER --}}
    <header class="header">

        <div class="left-header">
            <div class="logo-box">
                {{ auth()->check()
                    ? auth()->user()->Username
                    : 'Guest'
                }}
            </div>

            <div class="logo-icon">
                <img src="{{ asset('images/Trinity Logo Cropped.png') }}">
            </div>
        </div>

        <div class="search-wrapper">
            <form method="GET" action="{{ url('/admin/dashboard') }}" class="search-wrapper">

    <a href="{{ url('/admin/dashboard') }}" class="home-icon">
                <img src="{{ asset('Flaticon/home.png') }}">
            </a>

    <form method="GET" action="{{ route('admin.dashboard') }}">
    
        <div class="search-bar-wrapper">

            <div class="search-bar">

                <img
                    src="{{ asset('Flaticon/search.png') }}"
                    class="search-icon"
                >

                <input
                    type="text"
                    id="bookSearchInput"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Ada buku yang ingin dicari?"
                    autocomplete="off"
                >

            </div>

            <div
                class="search-suggestions"
                id="bookSearchSuggestions"
            ></div>

        </div>

        <button type="submit" class="btn-search">
            Cari
        </button>

    </form>



        </div>

        <div class="right-header">

            <a href="{{ route('admin.activity') }}" class="icon-btn">
                <img src="{{ asset('Flaticon/history (1).png') }}">
            </a>

            <button class="icon-btn logout" id="openLogout">
                <img src="{{ asset('Flaticon/turn-off.png') }}">
            </button>

        </div>

    </header>

    {{-- CONTENT --}}
    <main class="admin-container">

        {{-- top bar --}}
        <div class="top-bar">

            <div class="categories">

                <div class="row-1">

                    <button class="active">
                        Semua
                    </button>

                    @foreach($kategori as $item)

                        <button>
                            {{ $item->NamaKategori }}
                        </button>

                    @endforeach

                </div>

            </div>

            <div class="admin-actions">

              <button class="btn-report">
                  <img src="{{ asset('Flaticon/plus.png') }}">
                  <span>Laporan</span>
              </button>

              <button class="btn-book" id="openAddBook">
                  <img src="{{ asset('Flaticon/plus.png') }}">
                  <span>Buku</span>
              </button>

              <a href="{{ url('/admin/users') }}" class="btn-users-top">
                  <img src="{{ asset('Flaticon/team.png') }}">
                  <span>Kelola User</span>
              </a>

            </div>

        </div>

        {{-- stats --}}
        <div class="stats">

            <div class="stat-card">
                <h4>Total Buku</h4>
                <h2>{{ $totalBuku }}</h2>
            </div>

            <div class="stat-card">
                <h4>Buku dipinjam</h4>
                <h2>{{ $bukuDipinjam }}</h2>
            </div>

            <div class="stat-card">
                <h4>Dipinjam Bulan ini</h4>
                <h2>{{ $dipinjamBulanIni }}</h2>
            </div>

            <div class="stat-card">
                <h4>Pengembalian telat</h4>
                <h2>{{ $telat }}</h2>
            </div>

        </div>

        {{-- TABLE HEADER --}}
<div class="book-table-header">

    <div>Cover</div>

    <div>Judul Buku</div>

    <div>Penulis</div>

    <div>Kategori</div>

    <div>Stok</div>

    <div>Dipinjam</div>

</div>

        {{-- rows --}}
@foreach($buku as $index => $item)

@php
$ulasanData = $item->ulasan->map(function($u){
    return [
        "nama" => $u->user->NamaLengkap,
        "username" => $u->user->Username,

        "foto" => $u->user->Foto
            ? asset('storage/' . $u->user->Foto)
            : asset('images/default-profile.png'),

        "rating" => $u->Rating,
        "ulasan" => $u->Ulasan,

        "created_at" => $u->created_at
            ? $u->created_at->diffForHumans()
            : '-',
    ];
});
@endphp

<div class="book-row-wrapper">

    <div class="row-number">
        {{ $index + 1 }}
    </div>

    <div class="book-row"
    data-ulasan='@json($ulasanData)'

data-rating-summary='@json([
    "rating_average" => $item->rating_average,
    "rating_distribution" => $item->rating_distribution
])'
    data-book-id="{{ $item->BukuID }}"
    data-judul="{{ $item->Judul }}"
    data-penulis="{{ $item->Penulis }}"
    data-penerbit="{{ $item->Penerbit }}"
    data-tahun="{{ $item->TahunTerbit }}"
    data-kategori="{{ $item->kategoriRelasi
        ->pluck('kategori.NamaKategori')
        ->implode(', ') }}"
    data-deskripsi="{{ $item->Deskripsi }}"
    data-stok="{{ $item->Stok }}"
    data-cover="{{ $item->Cover
    ? asset('storage/books/' . $item->Cover)
    : asset('images/default-book.png') }}"
    data-dipinjam="{{ $item->peminjaman->where('StatusPeminjaman', 'Dipinjam')->count() }}"
>

{{-- COVER --}}
<div class="book-cover-thumb">

    <img
        src="{{ $item->Cover
            ? asset('storage/books/' . $item->Cover)
            : asset('images/default-book.png') }}"
        alt="cover"
    >

</div>

        {{-- JUDUL --}}
<div class="book-title">
    {{ $item->Judul }}
</div>

{{-- PENULIS --}}
<div class="book-small">
    {{ $item->Penulis }}
</div>

{{-- KATEGORI --}}
<div class="book-category">

    {{ $item->kategoriRelasi
        ->pluck('kategori.NamaKategori')
        ->implode(', ') }}

</div>

{{-- STOK --}}
<div class="book-stock">
    {{ $item->Stok }}
</div>

{{-- DIPINJAM --}}
<div class="book-borrowed">

    {{ $item->peminjaman
        ->where('StatusPeminjaman', 'Dipinjam')
        ->count() }}

</div>



    </div>

</div>

@endforeach

{{-- EMPTY SEARCH --}}
<div class="empty-search" id="emptySearch">
    <img src="{{ asset('Flaticon/search.png') }}">

    <h3>Buku tidak ditemukan</h3>

    <p>
        Coba gunakan judul lain atau ubah kategori pencarian.
    </p>
</div>

        {{-- footer --}}
        <div class="bottom-bar">

    @if ($buku->hasPages())

<div class="custom-pagination">

    {{-- Previous --}}
    @if ($buku->onFirstPage())
        <span class="disabled">&lt;</span>
    @else
        <a href="{{ $buku->previousPageUrl() }}">&lt;</a>
    @endif

    {{-- Page Number --}}
    @foreach ($buku->getUrlRange(1, $buku->lastPage()) as $page => $url)

        @if ($page == $buku->currentPage())
            <span class="active">{{ $page }}</span>
        @else
            <a href="{{ $url }}">{{ $page }}</a>
        @endif

    @endforeach

    {{-- Next --}}
    @if ($buku->hasMorePages())
        <a href="{{ $buku->nextPageUrl() }}">&gt;</a>
    @else
        <span class="disabled">&gt;</span>
    @endif

</div>

@endif

        </div>

    </main>

   <!-- DETAIL -->
<div class="book-detail-overlay" id="bookDetail">
    <div class="detail-container">

        <!-- LEFT -->
        <div class="detail-left">
            <button class="btn-back" id="closeDetail">
                <img src="{{ asset('Flaticon/back.png') }}" alt="back">
            </button>

            <h2 id="detailTitle">Laut Bercerita</h2>

            <img
                src="{{ asset('images/book1.png') }}"
                class="detail-cover"
                id="detailImage"
            />

            <button class="btn-review">Rating dan Ulasan</button>
            <div class="reviews" id="reviewsContainer"></div>

        </div>

        <!-- CENTER -->
        <div class="detail-center" id="detailCenter">

            <div class="detail-field">
                <label>Judul Buku</label>
                <span class="view-value" id="vJudul"></span>
                <input class="edit-input" id="eJudul">
            </div>

            <div class="detail-field">
                <label>Penulis</label>
                <span class="view-value" id="vPenulis"></span>
                <input class="edit-input" id="ePenulis">
            </div>

            <div class="detail-field">
                <label>Penerbit</label>
                <span class="view-value" id="vPenerbit"></span>
                <input class="edit-input" id="ePenerbit">
            </div>

            <div class="detail-field">
                <label>Tahun Terbit</label>
                <span class="view-value" id="vTahun"></span>
                <input class="edit-input" id="eTahun">
            </div>

            <div class="detail-field">
                <label>Kategori</label>
                <span class="view-value" id="vKategori"></span>
                <input class="edit-input" id="eKategori">
            </div>

            <div class="detail-field description-field">
                <label>Deskripsi</label>

                <p class="view-value" id="vDeskripsi"></p>

                <textarea
                    class="edit-input edit-textarea"
                    id="eDeskripsi"
                ></textarea>
            </div>

        </div>

        <!-- RIGHT -->
        <div class="detail-right">

            <div class="stok-box">
                <p>Stok : <span id="dStok">3</span></p>
                <p>Sedang dipinjam : <span id="dStatus">1</span></p>

                <!-- mode biasa -->
                <div class="detail-actions-view">
                    <button class="btn-delete-detail">
                        <img src="{{ asset('Flaticon/trash.png') }}" alt="hapus">
                        Hapus
                    </button>

                    <button class="btn-edit-detail" id="btnEditDetail">
                        <img src="{{ asset('Flaticon/compose.png') }}" alt="edit">
                        Edit
                    </button>
                </div>

                <!-- mode edit -->
                <div class="detail-actions-edit">
                    <button class="btn-save-detail" id="btnSaveDetail">
                        <img src="{{ asset('Flaticon/check.png') }}" alt="save">
                        Simpan
                    </button>

                    <button class="btn-cancel-detail" id="btnCancelDetail">
                        Batal
                    </button>
                </div>
            </div>

            <div class="rating-summary">

    <div class="rating-layout">

        <div class="rating-left">
            <h2 id="avgRating">0,0</h2>
            <p class="rating-label">dari 5</p>
        </div>

        <div class="rating-right">

            <div class="rating-row">
                <span>5</span>
                <div class="bar-bg">
                    <div id="bar-5" class="bar-fill"></div>
                </div>
                <span id="count-5">0</span>
            </div>

            <div class="rating-row">
                <span>4</span>
                <div class="bar-bg">
                    <div id="bar-4" class="bar-fill"></div>
                </div>
                <span id="count-4">0</span>
            </div>

            <div class="rating-row">
                <span>3</span>
                <div class="bar-bg">
                    <div id="bar-3" class="bar-fill"></div>
                </div>
                <span id="count-3">0</span>
            </div>

            <div class="rating-row">
                <span>2</span>
                <div class="bar-bg">
                    <div id="bar-2" class="bar-fill"></div>
                </div>
                <span id="count-2">0</span>
            </div>

            <div class="rating-row">
                <span>1</span>
                <div class="bar-bg">
                    <div id="bar-1" class="bar-fill"></div>
                </div>
                <span id="count-1">0</span>
            </div>

        </div>
    </div>
</div>

        </div>

    </div>
</div>

<!-- Tambah Buku -->
 <div class="book-detail-overlay" id="addBookOverlay">

    <div class="detail-container">

        <!-- LEFT -->
        <div class="detail-left">

            <button class="btn-back" id="closeAddBook">
                <img src="{{ asset('Flaticon/back.png') }}">
            </button>

            <h2>Judul Buku</h2>

            <label class="upload-cover-box">

                <input
                    type="file"
                    id="bookCoverInput"
                    hidden
                    accept="image/*"
                >

                <img
                    id="coverPreview"
                    style="display:none;"
                >

                <span id="uploadText">
                    Klik / Drag Cover
                </span>

            </label>

        </div>

        <!-- CENTER -->
        <div class="detail-center add-mode">

            <div class="detail-field">
                <label>Judul Buku</label>
                <input type="text" id="addJudul">
            </div>

            <div class="detail-field">
                <label>Penulis</label>
                <input type="text" id="addPenulis">
            </div>

            <div class="detail-field">
                <label>Penerbit</label>
                <input type="text" id="addPenerbit">
            </div>

            <div class="detail-field">
                <label>Tahun Terbit</label>
                <input type="number" id="addTahun">
            </div>

            <div class="detail-field">
                <label>
    Kategori
    <small>(CTRL + klik untuk pilih lebih dari satu)</small>
</label>

                <select id="addKategori" multiple>

                    @foreach($kategori as $item)

                    <option value="{{ $item->KategoriID }}">
                        {{ $item->NamaKategori }}
                    </option>

                    @endforeach

                </select>
            </div>

            <div class="detail-field description-field">

                <label>Deskripsi</label>

                <textarea id="addDeskripsi"></textarea>

            </div>

        </div>

        <!-- RIGHT -->
        <div class="detail-right">

            <div class="stok-box">

                <p>Stok Awal</p>

                <input
                    type="number"
                    id="addStock"
                    value="1"
                    class="stock-input"
                >

                <button class="btn-save-detail" id="submitAddBook">
                    <img src="{{ asset('Flaticon/plus.png') }}" alt="">
                    Tambah Buku
                </button>

            </div>

        </div>

    </div>

</div>

<!-- COLLECTION NOTIFICATION --> <div class="collection-toast" id="collectionToast"> <div class="collection-toast-content"> <img src="{{ asset('Flaticon/checked.png') }}" alt="success" id="collectionToastIcon" > <span id="collectionToastText"></span> </div> </div>

</body>
</html>
