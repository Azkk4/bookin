<!-- PREVIEW LAPORAN -->
<div class="preview-report-overlay" id="reportPreviewOverlay">

    <div class="preview-report-modal">

        <!-- LEFT -->
        <div class="preview-left">

            <!-- CONTENT -->
            <div class="preview-info-container">

                <div class="preview-info-title">
                    Detail Laporan
                </div>

                <div class="preview-info-card">

                    <div class="preview-info-list">
                        
                        <div class="preview-info-row">
                            <span class="preview-label">
                                Jenis Laporan
                            </span>

                            <strong
                                class="preview-value"
                                id="infoJenis"
                            >
                                -
                            </strong>
                        </div>

                        <div class="preview-info-row">
                            <span class="preview-label">
                                Periode
                            </span>

                            <strong
                                class="preview-value"
                                id="infoPeriode"
                            >
                                -
                            </strong>
                        </div>

                        <div class="preview-info-row">
                            <span class="preview-label">
                                Jumlah Data
                            </span>

                            <strong
                                class="preview-value"
                                id="infoTotal"
                            >
                                0
                            </strong>
                        </div>

                        <div class="preview-info-row">
                            <span class="preview-label">
                                Jumlah Halaman
                            </span>

                            <strong
                                class="preview-value"
                                id="infoPages"
                            >
                                1
                            </strong>
                        </div>

                        <div class="preview-info-row">
                            <span class="preview-label">
                                Dicetak Oleh
                            </span>

                            <strong
                                class="preview-value"
                                id="infoUser"
                            >
                                Admin
                            </strong>
                        </div>

                        <div class="preview-info-row">
                            <span class="preview-label">
                                Tanggal Cetak
                            </span>

                            <strong
                                class="preview-value"
                                id="infoTanggal"
                            >
                                -
                            </strong>
                        </div>

                    </div>
                
                </div>

                <p class="preview-note">
                    Laporan ini dihasilkan secara otomatis oleh sistem.
                    Pastikan data yang ditampilkan sudah sesuai
                    sebelum melakukan proses cetak atau ekspor PDF.
                </p>

                
            </div>

        </div>

        <!-- RIGHT -->
        <div class="preview-right">

            <div
                class="preview-paper"
                id="previewPaperContent"
            >

            </div>

        </div>

        <!-- BUTTON -->
            <div class="preview-buttons">

                <div class="preview-buttons-left">

                    <button
                        class="btn-preview-back"
                        id="closePreviewReport"
                    >
                        Kembali
                    </button>

                </div>

                <div class="preview-buttons-right">

                    <button
                        class="btn-preview-print"
                        id="printReportBtn"
                    >
                        Print
                    </button>

                    <button
                        class="btn-preview-pdf"
                        id="downloadPdf"
                    >
                        Simpan sebagai PDF
                    </button>

                </div>

            </div>

    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>