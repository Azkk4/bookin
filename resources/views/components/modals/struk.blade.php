<!-- MODAL STRUK -->
<div class="receipt-modal-overlay" id="receiptModal">
  <div class="receipt-modal">
    <!-- CLOSE -->
    <button class="close-receipt" id="closeReceipt">
      <img src="{{ asset('Flaticon/close.png') }}" alt="close" />
    </button>

    <!-- TITLE -->
    <h2>Aplikasi Perpustakaan</h2>

    <!-- CONTENT -->
    <div class="receipt-content">
      <!-- LEFT -->
      <div class="receipt-left">
        <img
          src="{{ asset('images/book1.png') }}"
          alt="book"
          class="receipt-cover"
          id="receiptBookImage"
        />

        <div class="receipt-status">
          <img src="{{ asset('Flaticon/checked.png') }}" alt="success" />

          <span id="receiptStatusTitle">
              Berhasil dipinjam
          </span>
        </div>
      </div>

      <!-- RIGHT -->
      <div class="receipt-right">
        <div class="receipt-row">
          <p>Judul Buku</p>
          <span>:</span>
          <h5 id="rJudul">Laut Bercerita</h5>
        </div>

        <div class="receipt-row">
          <p>Penulis</p>
          <span>:</span>
          <h5 id="rPenulis">Leila S. Chudori</h5>
        </div>

        <div class="receipt-row">
          <p>Penerbit</p>
          <span>:</span>
          <h5 id="rPenerbit">Kepustakaan Po..</h5>
        </div>

        <div class="receipt-row">
          <p>Tahun Terbit</p>
          <span>:</span>
          <h5 id="rTahun">2017</h5>
        </div>

        <div class="receipt-row">
          <p>Tanggal Peminjaman</p>
          <span>:</span>
          <h5 id="rTanggal">14/04/2026</h5>
        </div>

        <div class="receipt-row">
          <p>Waktu Peminjaman</p>
          <span>:</span>
          <h5 id="rDurasi">5 Hari</h5>
        </div>

        <div class="receipt-row">
          <p>Batas Pengembalian</p>
          <span>:</span>
          <h5 id="rKembali">19/04/2026</h5>
        </div>

        <div class="receipt-row">
          <p>Status</p>
          <span>:</span>
          <h5 class="success-text">Sedang dipinjam</h5>
        </div>
      </div>
    </div>
  </div>
</div>