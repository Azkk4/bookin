<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Aktivitas Admin</title>
  @vite([
      'resources/css/admin/activity.css',
    ])
</head>
<body>

<div class="activity-page">

    <div class="activity-header">

        <div class="activity-title">
            Riwayat Aktivitas Sistem
        </div>

        <a
            href="{{ route('admin.dashboard') }}"
            class="activity-back"
        >
            Kembali
        </a>

    </div>

      <div class="activity-stats">

      <div class="stat-card">
          <h2>{{ $totalLog }}</h2>
          <span>Total Aktivitas</span>
      </div>

      <div class="stat-card">
          <h2>{{ $roleChanges }}</h2>
          <span>Perubahan Role</span>
      </div>

      <div class="stat-card">
          <h2>{{ $bookChanges }}</h2>
          <span>Aktivitas Buku</span>
      </div>

      <div class="stat-card">
          <h2>{{ $statusChanges }}</h2>
          <span>Status User</span>
      </div>

  </div>

    <div class="activity-list">

        @foreach($logs as $log)

        <div class="activity-card">

            <div class="activity-icon">

                @if(str_contains($log->Aksi,'Tambah'))
                    ➕
                @elseif(str_contains($log->Aksi,'Hapus'))
                    ❌
                @elseif(str_contains($log->Aksi,'Role'))
                    🛡️
                @elseif(str_contains($log->Aksi,'Status'))
                    🔄
                @else
                    📌
                @endif

            </div>

            <div class="activity-content">

                <div class="activity-action">
                    {{ $log->Aksi }}
                </div>

                <div class="activity-description">
                    {{ $log->Deskripsi }}
                </div>

                <div class="activity-meta">

                    {{ $log->user->NamaLengkap ?? 'System' }}

                    •

                    {{ \Carbon\Carbon::parse($log->CreatedAt)->format('d M Y H:i') }}

                </div>

            </div>

        </div>

        @endforeach

    </div>

</div>
  
</body>
</html>