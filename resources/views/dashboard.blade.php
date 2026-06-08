<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Dashboard Peminjam</title>

    @vite([
      'resources/css/dashboard.css',
      'resources/css/logout.css',
      'resources/css/peminjaman.css',
      'resources/css/struk.css',
      'resources/css/history.css',
      'resources/css/pengembalian.css',

      'resources/js/action-confirm.js',
      'resources/js/dashboard.js',
      'resources/js/logout.js',
      'resources/js/peminjaman.js',
      'resources/js/struk.js',
      'resources/js/history.js',
      'resources/js/pengembalian.js',
    ])

    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
  </head>

  
  <body data-guest="{{ auth()->check() ? 'false' : 'true' }}">

    @auth
    @include('components.modals.logout')
    @include('components.modals.peminjaman')
    @include('components.modals.struk')
    @include('components.modals.history')
    @include('components.modals.pengembalian')
    @endauth
    @include('components.modals.action-confirm')

    <!-- HEADER -->
    <header class="header">
      <div class="left-header">
        <div class="logo-box">
            {{ auth()->check()
                ? auth()->user()->Username
                : 'Guest'
            }}
        </div>

        <div class="logo-icon">
          <img src="{{ asset('images/millennium_logo_cropped.png') }}" />
        </div>
      </div>

      <div class="search-wrapper">
        <button class="home-icon">
          <img src="{{ asset('Flaticon/home.png') }}" />
        </button>

        <div class="search-bar">
            <img
                src="{{ asset('Flaticon/search.png') }}"
                class="search-icon"
            />

            <input
                type="text"
                id="searchInput"
                placeholder="Ada buku yang ingin dicari?"
            />
        </div>

        <button class="btn-search" id="searchButton">
            Cari
        </button>
      </div>

      <div class="right-header">
        @auth
        <button class="icon-btn" id="openHistory">
            <img src="{{ asset('Flaticon/history (1).png') }}">
        </button>
        @endauth

        @if(auth()->check())

        <button class="icon-btn logout" id="openLogout">
            <img src="{{ asset('Flaticon/turn-off.png') }}">
        </button>

        @else

        <a href="{{ route('login') }}" class="icon-btn login">
            Login
        </a>

        @endif
      </div>
    </header>

    <!-- MAIN -->
    <main class="container">
      <!-- SIDEBAR -->
      @auth
      <aside class="sidebar" id="sidebar">

        <div class="sidebar-top" id="toggleSidebar">

          <div class="sidebar-header">

              <div class="collection-actions">

                <button class="collection-btn manage-btn" id="manageBtn">
                    <img src="{{ asset('Flaticon/compose.png') }}">
                    <span id="manageText">Kelola</span>
                </button>

                <button class="collection-btn cancel-btn" id="cancelManage">
                    Batal
                </button>

                <button class="collection-btn delete-btn" id="deleteBtn">
                    <img src="{{ asset('Flaticon/trash.png') }}">
                    Hapus
                </button>

              </div>

              <h2 class="sidebar-title">
                  Koleksi Kamu
              </h2>

          </div>

        </div>

        <div class="collection-content">

            @foreach($koleksi as $item)

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

            "created_at" => $u->created_at->diffForHumans(),
        ];
    });
@endphp

            <div class="book-slot"
                data-rating-summary='@json([
    "rating_average" => $item->rating_average,
    "rating_distribution" => $item->rating_distribution
])'
                data-id="{{ $item->BukuID }}"
                data-dipinjam="{{ in_array($item->BukuID, $sedangDipinjam) ? 'true' : 'false' }}"
                data-peminjaman="{{ $peminjamanAktif[$item->BukuID] ?? '' }}"
                data-koleksi="{{ $koleksi->contains('BukuID', $item->BukuID) ? 'true' : 'false' }}"
                data-ulasan='@json($ulasanData)'
                data-judul="{{ $item->Judul }}"
                data-penulis="{{ $item->Penulis }}"
                data-penerbit="{{ $item->Penerbit }}"
                data-tahun="{{ $item->TahunTerbit }}"
                data-deskripsi="{{ $item->Deskripsi }}"
                data-stok="{{ $item->Stok }}"
                data-cover="{{ asset('storage/books/' . $item->Cover) }}"
                data-kategori="{{ collect($item->kategoriRelasi)
    ->map(fn($rel) => $rel->kategori?->NamaKategori)
    ->filter()
    ->implode(', ') }}"
            >
            <input type="checkbox"
            class="collection-check"
            value="{{ $item->BukuID }}">
                <img
                    class="collection-cover"
                    src="{{ asset('storage/books/' . $item->Cover) }}"
                >
                <div class="book-info">
                    <h3>{{ $item->Judul }}</h3>

                    <h5>{{ $item->Penulis }}</h5>

                    <p>
                        {{ $item->kategoriRelasi->pluck('kategori.NamaKategori')->implode(', ') }}
                    </p>
                </div>
            </div>

            @endforeach

        </div>

        
      </aside>
      @endauth

      <!-- CONTENT -->
      <section class="content {{ auth()->guest() ? 'guest-mode' : '' }}">
        <div class="top-bar">
          <div class="categories">

          <div class="row-1">

              <button class="category-btn active"
                  data-category="Semua">
                  Semua
              </button>

              @foreach($kategori as $kat)

              <button
                  class="category-btn"
                  data-category="{{ $kat->NamaKategori }}"
              >
                  {{ $kat->NamaKategori }}
              </button>

              @endforeach

          </div>

      </div>

        @auth
        <button class="btn-return" id="openPengembalian">
            Kembalikan Buku
        </button>
        @endauth
        </div>

        <!-- GRID -->
        <div class="book-grid">
          <div class="empty-search" id="emptySearch">
    Buku tidak ditemukan
</div>

            @foreach($buku as $item)

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

            "created_at" => $u->created_at->diffForHumans(),
        ];
    });
@endphp

            <div class="book-card"
                data-rating-summary='@json([
    "rating_average" => $item->rating_average,
    "rating_distribution" => $item->rating_distribution
])'
                data-id="{{ $item->BukuID }}"
                data-dipinjam="{{ in_array($item->BukuID, $sedangDipinjam) ? 'true' : 'false' }}"
                data-peminjaman="{{ $peminjamanAktif[$item->BukuID] ?? '' }}"
                data-koleksi="{{ $koleksi->contains('BukuID', $item->BukuID) ? 'true' : 'false' }}"
                data-ulasan='@json($ulasanData)'
                data-judul="{{ $item->Judul }}"
                data-penulis="{{ $item->Penulis }}"
                data-penerbit="{{ $item->Penerbit }}"
                data-tahun="{{ $item->TahunTerbit }}"
                data-deskripsi="{{ $item->Deskripsi }}"
                data-stok="{{ $item->Stok }}"
                data-cover="{{ asset('storage/books/' . $item->Cover) }}"
                data-kategori="{{ collect($item->kategoriRelasi)
    ->map(fn($rel) => $rel->kategori?->NamaKategori)
    ->filter()
    ->implode(', ') }}"
            >
                <img src="{{ asset('storage/books/' . $item->Cover) }}" />
                <div class="book-info">
                    <h3>{{ $item->Judul }}</h3>
                    <h5>{{ $item->Penulis }}</h5>
                    <p>
                        {{ $item->kategoriRelasi->pluck('kategori.NamaKategori')->implode(', ') }}
                    </p>
                </div>
            </div>

            @endforeach

        </div>
      </section>
    </main>

    <!-- DETAIL -->
    <div class="book-detail-overlay" id="bookDetail">
      <div class="detail-panel">
      <div class="detail-container">

        <!-- LEFT -->
        <div class="detail-left">

          <button class="btn-back" id="closeDetail">
            <img src="{{ asset('Flaticon/back.png') }}" alt="back">
          </button>

          <h2 id="detailTitle">
            Laut Bercerita
          </h2>

          <img
            src="{{ asset('images/book1.png') }}" class="detail-cover"
            id="detailImage"
          />

          <button class="btn-review">
            Rating dan Ulasan
          </button>

          <div class="reviews" id="reviewsContainer"></div>
        </div>

        <!-- CENTER -->
        <div class="detail-center">
          <p><b>Judul Buku</b> : <span id="dJudul"></span></p>

          <p><b>Penulis</b> : <span id="dPenulis"></span></p>

          <p><b>Penerbit</b> : <span id="dPenerbit"></span></p>

          <p><b>Tahun Terbit</b> : <span id="dTahun"></span></p>

          <p><b>Kategori</b> : <span id="dKategori"></span></p>

          <p><b>Deskripsi :</b></p>

          <p id="dDeskripsi"></p>
        </div>

        <!-- RIGHT -->
        <div class="detail-right">

          <div class="stok-box">
            <p>Stok : <span id="dStok">3</span></p>

            <button class="btn-add" id="btnAddCollection" data-guest="{{ auth()->guest() ? 'true' : 'false' }}">
              <img src="{{ asset('Flaticon/plus.png') }}" alt="add">
              Tambah ke Koleksi
            </button>

            <button class="btn-pinjam" id="detailActionButton" data-guest="{{ auth()->guest() ? 'true' : 'false' }}">
                Pinjam
            </button>

          </div>

          <div class="rating-box">
            <div class="star-rating">
              <img src="{{ asset('Flaticon/star.png') }}" class="star" data-value="1" />
              <img src="{{ asset('Flaticon/star.png') }}" class="star" data-value="2" />
              <img src="{{ asset('Flaticon/star.png') }}" class="star" data-value="3" />
              <img src="{{ asset('Flaticon/star.png') }}" class="star" data-value="4" />
              <img src="{{ asset('Flaticon/star.png') }}" class="star" data-value="5" />
            </div>

            <div class="textarea-wrapper">
              <textarea
                id="reviewText"
                placeholder="Berikan pendapatmu di sini..."
              ></textarea>

              <button class="btn-send" id="btnSend" data-guest="{{ auth()->guest() ? 'true' : 'false' }}" disabled>
                <img src="{{ asset('Flaticon/send.png') }}" alt="send" />
              </button>
            </div>
          </div>

          <div class="rating-summary">

    <div class="rating-layout">

        <!-- LEFT SIDE (AVG) -->
        <div class="rating-left">
            <h2 id="avgRating">0,0</h2>
            <p class="rating-label">dari 5</p>
        </div>

        <!-- RIGHT SIDE (BARS) -->
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
      </div>

    </div>
    <!-- COLLECTION NOTIFICATION -->
        <div class="collection-toast" id="collectionToast">

            <div class="collection-toast-content">

                <img
                    src="{{ asset('Flaticon/checked.png') }}"
                    alt="success"
                    id="collectionToastIcon"
                >

                <span id="collectionToastText">
                    Berhasil ditambahkan ke koleksi
                </span>

            </div>

        </div>

        <!-- BORROW NOTIFICATION -->
<div class="borrow-toast" id="borrowToast">

    <div class="borrow-toast-content">

        <img
            src="{{ asset('Flaticon/checked.png') }}"
            alt="success"
            id="borrowToastIcon"
        >

        <span id="borrowToastText">
            Berhasil meminjam buku
        </span>

    </div>

</div>
  </body>
</html>