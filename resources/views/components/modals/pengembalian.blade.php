<div class="pengembalian-overlay" id="pengembalianModal">

    <div class="pengembalian-modal">

        <!-- TOP -->
        <div class="pengembalian-top">

            <div class="pengembalian-header">
                <h3>Pengembalian Buku</h3>
            </div>

            <button
                class="close-pengembalian"
                id="closePengembalian"
            >
                <img src="{{ asset('Flaticon/close.png') }}">
            </button>

        </div>

        <!-- CONTENT -->
        <div class="pengembalian-content">

            @forelse($dipinjam as $item)

            <div class="pengembalian-card">

                <!-- LEFT -->
                <div class="pengembalian-left">

                    <img
                        src="{{ asset('storage/books/' . $item->buku->Cover) }}"
                        class="pengembalian-cover"
                    >

                    <button
                        class="btn-kembalikan"
                        data-id="{{ $item->PeminjamanID }}"
                    >
                        Kembalikan Buku
                    </button>

                </div>

                <!-- RIGHT -->
                <div class="pengembalian-center">

                    <h4 class="judul-buku">
                        {{ $item->buku->Judul }}
                    </h4>

                    <div class="detail-group">

                        <div class="detail-row">
                            <div class="detail-label">Penulis</div>
                            <div class="detail-separator">:</div>

                            <div class="detail-value">
                                {{ $item->buku->Penulis }}
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Penerbit</div>
                            <div class="detail-separator">:</div>

                            <div class="detail-value">
                                {{ $item->buku->Penerbit }}
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Tanggal Pinjam</div>
                            <div class="detail-separator">:</div>

                            <div class="detail-value">
                                {{
                                    \Carbon\Carbon::parse(
                                        $item->TanggalPeminjaman
                                    )->format('d/m/Y')
                                }}
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Tanggal Kembali</div>
                            <div class="detail-separator">:</div>

                            <div class="detail-value">
                                {{
                                    \Carbon\Carbon::parse(
                                        $item->TanggalPengembalian
                                    )->format('d/m/Y')
                                }}
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Durasi</div>
                            <div class="detail-separator">:</div>

                            <div class="detail-value">
                                {{
                                    \Carbon\Carbon::parse(
                                        $item->TanggalPeminjaman
                                    )->diffInDays(
                                        \Carbon\Carbon::parse(
                                            $item->TanggalPengembalian
                                        )
                                    )
                                }} Hari
                            </div>
                        </div>

                    </div>

                    @php
                        $telat = now()->gt(
                            \Carbon\Carbon::parse($item->TanggalPengembalian)
                        );
                    @endphp

                    <div class="pengembalian-status-wrapper">

                        <div class="
                            status-pinjam
                            {{ $telat ? 'telat' : 'dipinjam' }}
                        ">

                            <span class="status-dot"></span>

                            {{ $telat ? 'Telat Pengembalian' : 'Sedang Dipinjam' }}

                        </div>

                    </div>

                </div>

            </div>

            @empty

            <div class="empty-pengembalian">
                Tidak ada buku yang sedang dipinjam
            </div>

            @endforelse

        </div>

    </div>

</div>