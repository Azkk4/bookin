<!-- GENERATE LAPORAN -->
<div class="report-modal-overlay" id="reportModal">

    <div class="report-modal">

        <!-- TOP -->
        <div class="report-top">

            <div class="report-header">
                <h3>Generate Laporan</h3>
            </div>

            <button class="close-report" id="closeReportModal">
                <img src="{{ asset('Flaticon/close.png') }}">
            </button>

        </div>

        <!-- CONTENT -->
        <div class="report-content">

            <p class="report-subtitle">
                Pilih salah satu dari tiga pilihan berikut
            </p>

            <!-- CARD -->
            <div class="report-cards">

                <!-- Buku -->
                <div class="report-card active" data-type="buku">

                    <h4>Laporan Buku</h4>

                    <img src="{{ asset('Flaticon/book.png') }}">

                    <p>Data Koleksi & Ketersediaan</p>

                </div>

                <!-- Anggota -->
                <div class="report-card" data-type="anggota">

                    <h4>Laporan Anggota</h4>

                    <img src="{{ asset('Flaticon/team.png') }}">

                    <p>Data User Perpustakaan</p>

                </div>

                <!-- Peminjaman -->
                <div class="report-card" data-type="peminjaman">

                    <h4>Laporan Peminjaman</h4>

                    <img src="{{ asset('Flaticon/borrow.png') }}">

                    <p>Data Transaksi & Aktivitas</p>

                </div>

            </div>

            <!-- DATE -->
            <div class="report-date-wrapper">

                <div class="date-title">
                    Rentang Waktu
                </div>

                <div class="report-date-row">

                    <!-- START -->
                    <div class="report-date-box">

                        <label>Tanggal Awal</label>

                        <div class="report-input-icon">

                            <input type="date" id="reportStart">

                            <img src="{{ asset('Flaticon/caret-down.png') }}">

                        </div>

                    </div>

                    <span class="date-separator">-</span>

                    <!-- END -->
                    <div class="report-date-box">

                        <label>Tanggal Akhir</label>

                        <div class="report-input-icon">

                            <input type="date" id="reportEnd">

                            <img src="{{ asset('Flaticon/caret-down.png') }}">

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- BUTTON -->
        <button
    class="btn-generate-report"
    id="generateReportBtn"
>
            Generate
        </button>

    </div>

</div>
