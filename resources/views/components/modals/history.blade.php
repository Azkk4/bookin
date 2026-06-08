<!-- MODAL HISTORY -->
<div class="history-modal-overlay" id="historyModal">

  <div class="history-modal">

    <!-- TOP -->
    <div class="history-top">

      <div class="history-header">
        <h3>Riwayat Aktivitas</h3>
      </div>

      <button class="close-history" id="closeHistory">
        <img src="{{ asset('Flaticon/close.png') }}" alt="close">
      </button>

    </div>

    <!-- CONTENT -->
    <!-- CONTENT -->
    <div class="history-content">

        @forelse($history as $item)

        <button
            class="history-item receipt-trigger"
            data-id="{{ $item->PeminjamanID }}"

            data-judul="{{ $item->buku->Judul }}"
            data-penulis="{{ $item->buku->Penulis }}"
            data-penerbit="{{ $item->buku->Penerbit }}"
            data-tahun="{{ $item->buku->TahunTerbit }}"

            data-cover="{{ asset('storage/books/' . $item->buku->Cover) }}"

            data-pinjam="{{ \Carbon\Carbon::parse($item->TanggalPeminjaman)->format('d/m/Y') }}"

            data-kembali="{{ \Carbon\Carbon::parse($item->TanggalPengembalian)->format('d/m/Y') }}"

            data-status="{{ $item->StatusPeminjaman }}"

        >

            <div class="history-left">

                <h4>

                    {{ $item->StatusPeminjaman }}

                    {{ $item->buku->Judul }}

                </h4>

            </div>

            <div class="history-right">

                {{ \Carbon\Carbon::parse($item->TanggalPeminjaman)->format('d/m/Y') }}

                -

                {{ \Carbon\Carbon::parse($item->TanggalPengembalian)->format('d/m/Y') }}

            </div>

        </button>

        @empty

        <div class="history-empty">
            Belum ada riwayat peminjaman
        </div>

        @endforelse

    </div>

  </div>

</div>