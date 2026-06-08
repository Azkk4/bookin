<div class="report-kop">

    <h2>BOOKIN'</h2>

    <h3>Sistem Informasi Perpustakaan Digital</h3>

    <p>Developed by CodeHub</p>

    <hr>

    <h4>{{ $judul }}</h4>

</div>

<div class="report-meta">

    <p><strong>Periode :</strong> {{ \Carbon\Carbon::parse($start)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($end)->translatedFormat('d F Y') }}</p>

    <p><strong>Jumlah Data :</strong> {{ $total }}</p>

    <p><strong>Tanggal Cetak :</strong> {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>

</div>