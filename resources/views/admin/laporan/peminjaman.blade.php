@include('admin.laporan.partials.header',[
    'judul' => 'LAPORAN DATA PEMINJAMAN'
])

<table class="report-table">

    <thead>
        <tr>
            <th>No</th>
            <th>Peminjam</th>
            <th>Buku</th>
            <th>Tgl Pinjam</th>
            <th>Batas Kembali</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody>

        @foreach($data as $index => $item)

        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item->user->NamaLengkap ?? '-' }}</td>
            <td>{{ $item->buku->Judul ?? '-' }}</td>
            <td>{{ $item->TanggalPeminjaman }}</td>
            <td>{{ $item->TanggalPengembalian }}</td>
            <td>{{ $item->StatusPeminjaman }}</td>
        </tr>

        @endforeach

    </tbody>

</table>

<div class="laporan-footer">

    <div class="laporan-summary">

        <h4>Ringkasan Laporan</h4>

        <p>Masih Dipinjam : {{ $summary['dipinjam'] }}</p>

        <p>Sudah Dikembalikan : {{ $summary['dikembalikan'] }}</p>

        <p>Terlambat : {{ $summary['terlambat'] }}</p>

    </div>

    <div class="ttd-area">

        <p>
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </p>

        <br><br><br>

        <strong>Petugas</strong>

    </div>

</div>