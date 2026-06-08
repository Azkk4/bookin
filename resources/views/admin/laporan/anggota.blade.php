@include('admin.laporan.partials.header',[
    'judul' => 'LAPORAN DATA ANGGOTA'
])

<table class="report-table">

    <thead>
        <tr>
            <th>No</th>
            <th>Nama Lengkap</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody>

        @foreach($data as $index => $user)

        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $user->NamaLengkap }}</td>
            <td class="email-column">{!! str_replace('@', '<wbr>@', e($user->Email)) !!}</td>
            <td>{{ $user->Role }}</td>
            <td>{{ $user->Status }}</td>
        </tr>

        @endforeach

    </tbody>

</table>

<div class="laporan-footer">

    <div class="laporan-summary">

        <h4>Ringkasan Laporan</h4>

        <p>Admin : {{ $summary['totalAdmin'] }}</p>

        <p>Petugas : {{ $summary['totalPetugas'] }}</p>

        <p>Peminjam : {{ $summary['totalPeminjam'] }}</p>

        <p>Akun Aktif : {{ $summary['totalAktif'] }}</p>

        <p>Akun Nonaktif : {{ $summary['totalNonaktif'] }}</p>

    </div>

    <div class="ttd-area">

        <p>
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </p>

        <br><br><br>

        <strong>Petugas</strong>

    </div>

</div>