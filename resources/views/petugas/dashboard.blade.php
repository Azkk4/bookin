<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Dashboard Petugas</title>

    @vite([
      'resources/css/admin/dashboard.css',
      'resources/css/admin/generate-laporan.css',
      'resources/css/logout.css',

      'resources/js/logout.js',
      'resources/js/action-confirm.js',
      'resources/js/petugas/dashboard.js',
      'resources/js/admin/generate-laporan.js'
    ])

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
</head>

<body>

@include('components.modals.logout')
@include('components.modals.generate-laporan')
@include('components.modals.action-confirm')

<header class="header">

    <div class="left-header">

        <div class="logo-box">
            {{ auth()->check()
                    ? auth()->user()->Username
                    : 'Guest'
                }}
        </div>

        <div class="logo-icon">
            <img src="{{ asset('images/gehenna_logo_cropped.png') }}">
        </div>

    </div>

    <div class="search-wrapper">

        <button class="home-icon">
            <img src="{{ asset('Flaticon/home.png') }}">
        </button>

        <div class="search-bar">

            <img src="{{ asset('Flaticon/search.png') }}" class="search-icon">

            <input type="text" placeholder="ada buku yang ingin dicari?">

        </div>

        <button class="btn-search">Cari</button>

    </div>

    <div class="right-header">

        <button class="icon-btn">
            <img src="{{ asset('Flaticon/history (1).png') }}">
        </button>

        <button class="icon-btn logout" id="openLogout">
            <img src="{{ asset('Flaticon/turn-off.png') }}">
        </button>

    </div>

</header>

<main class="admin-container">

    {{-- TOP BAR --}}
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

        {{-- PETUGAS HANYA LAPORAN --}}
        <div class="admin-actions">

            <button class="btn-report">
                <img src="{{ asset('Flaticon/plus.png') }}">
                <span>Laporan</span>
            </button>

        </div>

    </div>

    {{-- STATS --}}
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

    <div class="book-table-header">

        <div>Cover</div>

        <div>Judul Buku</div>

        <div>Penulis</div>

        <div>Kategori</div>

        <div>Stok</div>

        <div>Dipinjam</div>

    </div>

    {{-- ROWS --}}
    @foreach($buku as $index => $item)

    <div class="book-row-wrapper">

        <div class="row-number">
            {{ $loop->iteration }}
        </div>

        <div class="book-row"
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
            data-dipinjam="{{ $item->peminjaman
                ->where('StatusPeminjaman','Dipinjam')
                ->count() }}"
            data-ulasan='@json($item->ulasan_data)'

            data-rating-summary='@json([
                "rating_average" => $item->rating_average,
                "rating_distribution" => $item->rating_distribution
            ])'
            data-kategori="{{ $item->kategoriRelasi
            ->pluck('kategori.NamaKategori')
            ->implode(', ') }}"
        >

            <div class="book-cover-thumb">

                <img
                    src="{{ $item->Cover
                        ? asset('storage/books/' . $item->Cover)
                        : asset('images/default-book.png') }}"
                >

            </div>

            <div class="book-title">
                {{ $item->Judul }}
            </div>

            <div class="book-small">
                {{ $item->Penulis }}
            </div>

            <div class="book-category">

                {{ $item->kategoriRelasi
                    ->pluck('kategori.NamaKategori')
                    ->implode(', ') }}

            </div>

            <div class="book-stock">
                {{ $item->Stok }}
            </div>

            <div class="book-borrowed">

                {{ $item->peminjaman
                    ->where('StatusPeminjaman','Dipinjam')
                    ->count() }}

            </div>

        </div>

    </div>

    @endforeach
    <div class="empty-search" id="emptySearch">
    Buku tidak ditemukan
</div>

    {{-- FOOTER --}}
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

{{-- DETAIL BUKU --}}
<div class="book-detail-overlay" id="bookDetail">

    <div class="detail-container">

        {{-- LEFT --}}
        <div class="detail-left">

            <button class="btn-back" id="closeDetail">
                <img src="{{ asset('Flaticon/back.png') }}">
            </button>

            <h2 id="detailTitle"></h2>

            <img
                id="detailImage"
                class="detail-cover"
            >

            <button class="btn-review">
                Rating dan Ulasan
            </button>

            <div class="reviews" id="reviewsContainer"></div>

        </div>

        {{-- CENTER --}}
        <div class="detail-center">

            <div class="detail-field">
                <label>Judul Buku</label>
                <span class="view-value" id="vJudul"></span>
            </div>

            <div class="detail-field">
                <label>Penulis</label>
                <span class="view-value" id="vPenulis"></span>
            </div>

            <div class="detail-field">
                <label>Penerbit</label>
                <span class="view-value" id="vPenerbit"></span>
            </div>

            <div class="detail-field">
                <label>Tahun Terbit</label>
                <span class="view-value" id="vTahun"></span>
            </div>

            <div class="detail-field">
                <label>Kategori</label>
                <span class="view-value" id="vKategori"></span>
            </div>

            <div class="detail-field description-field">

                <label>Deskripsi</label>

                <p
                    class="view-value"
                    id="vDeskripsi"
                ></p>

            </div>

        </div>

        {{-- RIGHT --}}
        <div class="detail-right">

            <div class="stok-box">

                <p>
                    Stok Buku
                </p>

                <input
                    type="number"
                    id="stockInput"
                    class="stock-input"
                >

                <button class="btn-save-detail">

                    <img src="{{ asset('Flaticon/check.png') }}">

                    Simpan Stok

                </button>

            </div>

            <div class="rating-summary">

                <div class="rating-layout">

                    <div class="rating-left">
                        <h2 id="avgRating">0.0</h2>
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

</body>
</html>