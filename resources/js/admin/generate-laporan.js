document.addEventListener("DOMContentLoaded", () => {
    const reportModal = document.getElementById("reportModal");

    const previewOverlay = document.getElementById("reportPreviewOverlay");

    const openReport = document.querySelector(".btn-report");

    const closeReport = document.getElementById("closeReportModal");

    const closePreview = document.getElementById("closePreviewReport");

    const cards = document.querySelectorAll(".report-card");

    const generateBtn = document.getElementById("generateReportBtn");

    const startInput = document.getElementById("reportStart");

    const endInput = document.getElementById("reportEnd");

    const printBtn = document.getElementById("printReportBtn");

    const pdfBtn = document.getElementById("downloadPdf");

    const today = new Date().toISOString().split("T")[0];

    endInput.value = today;

    /* =========================
       OPEN
    ========================= */

    if (openReport) {
        openReport.addEventListener("click", () => {
            reportModal.classList.add("active");
        });
    }

    /* =========================
       CLOSE
    ========================= */

    if (closeReport) {
        closeReport.addEventListener("click", () => {
            reportModal.classList.remove("active");
        });
    }

    if (closePreview) {
        closePreview.addEventListener("click", () => {
            previewOverlay.classList.remove("active");

            reportModal.classList.add("active");
        });
    }

    /* =========================
       SELECT CARD
    ========================= */

    cards.forEach((card) => {
        card.addEventListener("click", () => {
            cards.forEach((c) => {
                c.classList.remove("active");
            });

            card.classList.add("active");
        });
    });

    /* =========================
       GENERATE
    ========================= */

    if (generateBtn) {
        generateBtn.addEventListener("click", () => {
            const activeCard = document.querySelector(".report-card.active");

            if (!activeCard) {
                alert("Pilih jenis laporan.");

                return;
            }

            const type = activeCard.dataset.type;

            const start = startInput.value;

            const end = endInput.value;

            /* VALIDASI */

            if (!start || !end) {
                alert("Pilih rentang tanggal.");
                return;
            }

            if (start > end) {
                alert(
                    "Tanggal awal tidak boleh lebih besar dari tanggal akhir.",
                );

                return;
            }

            /* =========================
   FETCH PREVIEW
========================= */

            fetch(
                `/admin/laporan/preview?type=${type}&start=${start}&end=${end}`,
            )
                .then((response) => response.json())

                .then((result) => {
                    if (!result.success) {
                        alert("Data laporan tidak ditemukan.");
                        return;
                    }

                    reportModal.classList.remove("active");

                    previewOverlay.classList.add("active");

                    document.getElementById("infoJenis").innerText =
                        type.toUpperCase();

                    document.getElementById("infoPeriode").innerText =
                        `${start} s/d ${end}`;

                    document.getElementById("infoUser").innerText = "Admin";

                    document.getElementById("infoTotal").innerText =
                        result.total;

                    document.getElementById("infoTanggal").innerText =
                        new Date().toLocaleDateString("id-ID");

                    document.getElementById("previewPaperContent").innerHTML =
                        result.html;

                    setTimeout(() => {
                        const paper = document.getElementById(
                            "previewPaperContent",
                        );

                        const totalPages = Math.ceil(paper.scrollHeight / 1123);

                        document.getElementById("infoPages").innerText =
                            totalPages;
                    }, 100);
                })

                .catch((error) => {
                    console.error(error);

                    alert("Gagal mengambil data laporan.");
                });
        });
    }

    if (printBtn) {
        printBtn.addEventListener("click", () => {
            const content = document.getElementById(
                "previewPaperContent",
            ).innerHTML;

            const win = window.open("", "_blank", "width=1200,height=900");

            win.document.write(`
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">

<title>Laporan</title>

<style>
*{
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}

body{
    font-family: Arial, sans-serif;
    margin:0;
    padding:30px;
    background:white;
    color:#000;
}

/* KOP */

.report-kop{
    text-align:center;
}

.report-kop h2{
    font-size:22px;
    margin-bottom:5px;
}

.report-kop h3{
    font-size:18px;
    margin-bottom:10px;
}

.report-kop hr{
    margin:10px 0;
}

.report-kop h4{
    margin-top:15px;
    font-size:18px;
    font-weight:500;
}

/* META */

.report-meta{
    margin-top:25px;
    margin-bottom:25px;
    font-size:14px;
    line-height:1.8;
}

/* TABLE */

.report-table{
    width:100%;
    margin-top:20px;
    border-collapse:collapse;
}

.report-table th{
    background:#e8f4ff;
    font-weight:600;
}

.report-table th,
.report-table td{
    border:1px solid #000;
    padding:10px;
    text-align:left;
    font-size:13px;
}

/* FOOTER */

.laporan-footer{
    margin-top:40px;
    display:flex;
    justify-content:space-between;
}

.laporan-summary h4{
    margin-bottom:10px;
}

.laporan-summary p{
    font-size:13px;
    color:#555;
}

.ttd-area{
    width:220px;
    text-align:center;
    font-size:13px;
}

@page{
    size:A4;
    margin:15mm;
}

</style>

</head>

<body>

${content}

</body>

</html>
        `);

            win.document.close();

            win.onload = () => {
                win.focus();

                setTimeout(() => {
                    win.print();
                }, 300);
            };
        });
    }

    if (pdfBtn) {
        pdfBtn.addEventListener("click", () => {
            const original = document.getElementById("previewPaperContent");

            const clone = original.cloneNode(true);

            clone.style.width = "190mm";
            clone.style.minHeight = "auto";
            clone.style.padding = "10mm";
            clone.style.margin = "0";
            clone.style.transform = "none";
            clone.style.boxShadow = "none";

            document.body.appendChild(clone);

            html2pdf()
                .set({
                    margin: 10,

                    filename: `laporan-${today}.pdf`,

                    image: {
                        type: "jpeg",
                        quality: 1,
                    },

                    html2canvas: {
                        scale: 2,
                    },

                    jsPDF: {
                        unit: "mm",
                        format: "a4",
                        orientation: "portrait",
                    },
                })
                .from(clone)
                .save()
                .then(() => {
                    clone.remove();
                });
        });
    }
});
