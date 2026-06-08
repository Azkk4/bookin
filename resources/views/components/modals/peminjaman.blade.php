<!-- MODAL PEMINJAMAN -->
<div class="borrow-modal-overlay" id="borrowModal">
    <div class="borrow-modal">

        <!-- HEADER -->
        <div class="borrow-top">

            <div class="borrow-header">
                <h3>Rincian Peminjaman</h3>
            </div>

            <button class="close-borrow" id="closeBorrow">
                <img src="{{ asset('Flaticon/close.png') }}" alt="close">
            </button>

        </div>

        <!-- CONTENT -->
        <div class="borrow-content">

            <!-- LEFT -->
            <div class="borrow-left">

                <h2 id="borrowBookTitle">
                    -
                </h2>

                <img
                    src=""
                    alt="book"
                    class="borrow-cover"
                    id="borrowBookImage"
                >

            </div>

            <!-- RIGHT -->
            <div class="borrow-right">

                <!-- tanggal -->
                <div class="borrow-group">

                    <label>Tanggal Peminjaman</label>

                    <div class="input-icon">

                        <input type="date" id="borrowDate">

                        <img src="{{ asset('Flaticon/caret-down.png') }}">

                    </div>

                </div>

                <!-- durasi -->
                <div class="borrow-group">

                    <label>Waktu Peminjaman</label>

                    <div class="input-icon">

                        <select id="borrowDuration">
                            <option value="1">1 Hari</option>
                            <option value="2">2 Hari</option>
                            <option value="3">3 Hari</option>
                            <option value="4">4 Hari</option>
                            <option value="5">5 Hari</option>
                            <option value="6">6 Hari</option>
                            <option value="7">7 Hari</option>
                        </select>

                        <img src="{{ asset('Flaticon/caret-down.png') }}">

                    </div>

                </div>

                <!-- pengembalian -->
                <div class="borrow-group">

                    <label>Batas Pengembalian</label>

                    <input type="text" id="returnDate" readonly>

                </div>

                <!-- tombol -->
                <button
                    type="button"
                    class="btn-confirm-borrow"
                >
                    Pinjam
                </button>

            </div>

        </div>

    </div>
</div>