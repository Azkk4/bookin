@include('admin.laporan.partials.header',[
    'judul' => 'LAPORAN DATA BUKU'
])

<table class="report-table">

    <thead>
        <tr>
            <th>No</th>
            <th>Judul</th>
            <th>Penulis</th>
            <th>Penerbit</th>
            <th>Tahun</th>
            <th>Stok</th>
        </tr>
    </thead>

    <tbody>

        @foreach($data as $index => $buku)

        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $buku->Judul }}</td>
            <td>{{ $buku->Penulis }}</td>
            <td>{{ $buku->Penerbit }}</td>
            <td>{{ $buku->TahunTerbit }}</td>
            <td>{{ $buku->Stok }}</td>
        </tr>

        @endforeach

    </tbody>

</table>

<div class="laporan-footer">

    <div class="laporan-summary">

        <h4>Ringkasan Laporan</h4>

        <p>Total Judul Buku : {{ $summary['totalJudul'] }}</p>

        <p>Buku Sedang Dipinjam : {{ $summary['dipinjam'] }}</p>

        <p>Buku Stok Kosong : {{ $summary['stokKosong'] }}</p>

    </div>

    <div class="ttd-area">

        <p>
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </p>

        <br><br><br>

        <strong>Petugas</strong>

    </div>

</div>