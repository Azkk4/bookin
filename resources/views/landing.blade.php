<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Perpustakaan Digital</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
  </head>
  <body>
    <!-- NAVBAR -->
    <header class="navbar">
      <div class="logo">BookIn'</div>
      <nav>
        <a href="#">Home</a>
        <a href="#features">Features</a>
        <a href="#">Roles</a>
        <a href="#">About</a>
      </nav>
    </header>

    <!-- HERO -->
    <section class="hero">
      <video autoplay muted loop playsinline class="bg-video">
        <source src="{{ asset('videos/schale.mp4') }}" type="video/mp4" />
      </video>

      <div class="overlay"></div>

      <div class="hero-content">
        <h1>Jelajahi Dunia Pengetahuan Tanpa Batas</h1>
        <p>Aplikasi Perpustakaan Digital oleh Kelompok 1</p>

        <div class="search-box">
          <div class="input-wrapper">
            <img src="{{ asset('Flaticon/search.png') }}" class="search-icon" alt="" />
            <input type="text" placeholder="ada buku yang ingin dicari?" />
          </div>
        </div>

        <div class="buttons">
          @if (Route::has('login'))
            @auth
            <a href="/dashboard" class="btn primary">
                Mulai Sekarang
            </a>
            @else
            <a href="{{ route('books') }}" class="btn primary">
                Mulai Sekarang
            </a>
            @endauth
            <a href="#features" class="btn secondary">Jelajahi Aplikasi</a>
          @endif
        </div>
      </div>
    </section>

    <!-- FEATURES -->
    <section class="features" id="features">
      <div class="feature-box">
        <div class="icon">📚</div>
        <div>
          <h3>Manajemen Buku</h3>
        </div>
        <div>
          <p>
            Memudahkan dalam mengelola seluruh data buku yang ada di
            perpustakaan.
          </p>
        </div>
      </div>

      <div class="feature-box">
        <div class="icon">📖</div>
        <div>
          <h3>Peminjaman & Pengembalian</h3>
        </div>
        <div>
          <p>Memudahkan proses peminjaman dan pengembalian buku</p>
        </div>
      </div>

      <div class="feature-box">
        <div class="icon">🔒</div>
        <div>
          <h3>Sistem Hak Akses</h3>
        </div>
        <div>
          <p>Menjaga keamanan untuk membedakan peran pengguna</p>
        </div>
      </div>

      <div class="feature-box">
        <div class="icon">🔍</div>
        <div>
          <h3>Pencarian Buku</h3>
        </div>
        <div>
          <p>Membantu pengguna menemukan buku dengan cepat.</p>
        </div>
      </div>
    </section>

    <!-- ROLES -->
    <section class="roles" id="roles">
      <div class="role-card admin">
        <div class="icon">
          <img src="{{ asset('images/Trinity Logo Cropped.png') }}" class="logo-admin" alt="Admin" />
        </div>
        <h3>Administrator</h3>
        <p>Sang Admin yang mengendalikan seluruh sistem perpustakaan.</p>
      </div>

      <div class="role-card petugas">
        <div class="icon">
          <img
            src="{{ asset('images/Trinity Logo Cropped.png') }}"
            class="logo-petugas"
            alt="Petugas"
          />
        </div>
        <h3>Petugas</h3>
        <p>Pengelola yang memastikan semua berjalan dengan tertib.</p>
      </div>

      <div class="role-card peminjam">
        <div class="icon">
          <img
            src="{{ asset('images/millennium_logo_cropped.png') }}"
            class="logo-peminjam"
            alt="Peminjam"
          />
        </div>
        <h3>Peminjam</h3>
        <p>Pengguna yang menjelajahi dunia pengetahuan melalui buku.</p>
      </div>
    </section>

    <!-- OUR TEAM -->
    <section class="team">
      <div class="overlay2"></div>

      <h2>Our Team</h2>

      <div class="team-list">
        <div class="team-item">Abdal Ubaidillah</div>
        <div class="team-item">al Azka Rezvan Muhammad</div>
        <div class="team-item">Arni Hoernisa</div>
        <div class="team-item">Bianca Indah Sari</div>
      </div>
    </section>
  </body>
</html>
