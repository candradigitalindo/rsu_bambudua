@extends('layouts.app')
@section('title', 'Paket Pasien')

@push('style')
<style>
    .stat-card { background: #fff; border-radius: 10px; padding: 16px 20px; border-left: 4px solid; transition: transform 0.2s; cursor: pointer; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
    .stat-card.active-filter { box-shadow: 0 0 0 2px rgba(13,110,253,0.5); }
    .stat-card .stat-value { font-size: 1.5rem; font-weight: 700; line-height: 1.2; }
    .stat-card .stat-label { font-size: 0.78rem; color: #6c757d; }
    .progress-mini { height: 6px; border-radius: 3px; background: #e9ecef; overflow: hidden; width: 80px; }
    .progress-mini .bar { height: 100%; border-radius: 3px; transition: width 0.3s; }
    .patient-avatar { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; color: #fff; flex-shrink: 0; }
    .session-display { display: flex; align-items: center; gap: 8px; }
    .session-text { font-size: 0.85rem; font-weight: 600; white-space: nowrap; }
    .date-range { font-size: 0.78rem; color: #6c757d; line-height: 1.4; }
    .date-range .sisa { display: inline-flex; align-items: center; gap: 2px; color: #e65100; font-weight: 600; font-size: 0.75rem; }
    .paket-row { transition: background-color 0.15s; }
    .paket-row:hover { background-color: #f8f9ff; }
    .status-pill { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .status-aktif { background: #e3f2fd; color: #1565c0; }
    .status-selesai { background: #e8f5e9; color: #2e7d32; }
    .status-expired { background: #fff3e0; color: #e65100; }
    .status-batal { background: #fce4ec; color: #c62828; }
    .empty-state { padding: 60px 20px; text-align: center; }
    .empty-state i { font-size: 4rem; color: #dee2e6; margin-bottom: 16px; }
</style>
@endpush

@section('content')
    {{-- Stats Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-lg col-md-4 col-6">
            <a href="{{ route('paket-pemeriksaan.pasien.index') }}" class="text-decoration-none">
                <div class="stat-card {{ !request('status') ? 'active-filter' : '' }}" style="border-color: #0d6efd;">
                    <div class="stat-value text-primary">{{ $stats['total'] }}</div>
                    <div class="stat-label">Total Paket</div>
                </div>
            </a>
        </div>
        <div class="col-lg col-md-4 col-6">
            <a href="{{ route('paket-pemeriksaan.pasien.index', ['status' => 'aktif']) }}" class="text-decoration-none">
                <div class="stat-card {{ request('status') == 'aktif' ? 'active-filter' : '' }}" style="border-color: #1565c0;">
                    <div class="stat-value" style="color: #1565c0;">{{ $stats['aktif'] }}</div>
                    <div class="stat-label">Aktif</div>
                </div>
            </a>
        </div>
        <div class="col-lg col-md-4 col-6">
            <a href="{{ route('paket-pemeriksaan.pasien.index', ['status' => 'selesai']) }}" class="text-decoration-none">
                <div class="stat-card {{ request('status') == 'selesai' ? 'active-filter' : '' }}" style="border-color: #2e7d32;">
                    <div class="stat-value" style="color: #2e7d32;">{{ $stats['selesai'] }}</div>
                    <div class="stat-label">Selesai</div>
                </div>
            </a>
        </div>
        <div class="col-lg col-md-4 col-6">
            <a href="{{ route('paket-pemeriksaan.pasien.index', ['status' => 'expired']) }}" class="text-decoration-none">
                <div class="stat-card {{ request('status') == 'expired' ? 'active-filter' : '' }}" style="border-color: #e65100;">
                    <div class="stat-value" style="color: #e65100;">{{ $stats['expired'] }}</div>
                    <div class="stat-label">Expired</div>
                </div>
            </a>
        </div>
        <div class="col-lg col-md-4 col-6">
            <a href="{{ route('paket-pemeriksaan.pasien.index', ['status' => 'batal']) }}" class="text-decoration-none">
                <div class="stat-card {{ request('status') == 'batal' ? 'active-filter' : '' }}" style="border-color: #c62828;">
                    <div class="stat-value" style="color: #c62828;">{{ $stats['batal'] }}</div>
                    <div class="stat-label">Batal</div>
                </div>
            </a>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="card mb-3">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0">
                    <i class="ri-user-heart-line me-1"></i> Paket Pasien
                    @if(request('status'))
                        <span class="badge bg-light text-dark fw-normal ms-1" style="font-size: 0.7rem;">
                            Filter: {{ ucfirst(request('status')) }}
                            <a href="{{ route('paket-pemeriksaan.pasien.index') }}" class="text-danger ms-1" title="Hapus filter">
                                <i class="ri-close-line"></i>
                            </a>
                        </span>
                    @endif
                </h5>
                <div class="d-flex gap-2 align-items-center">
                    <form method="GET" action="{{ route('paket-pemeriksaan.pasien.index') }}" class="d-flex gap-2">
                        <div class="search-container">
                            <input type="text" class="form-control" name="search"
                                placeholder="Cari nama/RM pasien..." value="{{ request('search') }}">
                            <i class="ri-search-line"></i>
                        </div>
                        @if(request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                    </form>
                    <a href="{{ route('paket-pemeriksaan.pasien.create') }}" class="btn btn-primary">
                        <i class="ri-user-add-line"></i>
                        <span class="btn-text">Berikan Paket</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width: 200px;">Pasien</th>
                            <th style="min-width: 150px;">Paket</th>
                            <th class="text-center" style="width: 160px;">Progress Sesi</th>
                            <th style="width: 130px;">Harga</th>
                            <th style="width: 150px;">Masa Berlaku</th>
                            <th class="text-center" style="width: 90px;">Status</th>
                            <th class="text-center" style="width: 90px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paketPasiens as $pp)
                            @php
                                $pct = $pp->total_sesi > 0 ? round(($pp->sesi_terpakai / $pp->total_sesi) * 100) : 0;
                                $colors = ['aktif' => '#1565c0', 'selesai' => '#2e7d32', 'expired' => '#e65100', 'batal' => '#c62828'];
                                $bgColors = ['aktif' => '#e3f2fd', 'selesai' => '#e8f5e9', 'expired' => '#fff3e0', 'batal' => '#fce4ec'];
                                $avatarColors = ['#1565c0','#2e7d32','#7b1fa2','#c62828','#00838f','#ef6c00','#4527a0','#00695c'];
                                $avatarIdx = crc32($pp->pasien->name ?? 'X') % count($avatarColors);
                                $initials = collect(explode(' ', $pp->pasien->name ?? 'X'))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->join('');
                            @endphp
                            <tr class="paket-row">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="patient-avatar" style="background: {{ $avatarColors[$avatarIdx] }};">
                                            {{ $initials }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $pp->pasien->name ?? '-' }}</div>
                                            <small class="text-muted">RM: {{ $pp->pasien->rekam_medis ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $pp->paketPemeriksaan->name ?? '-' }}</div>
                                </td>
                                <td class="text-center">
                                    <div class="session-display justify-content-center">
                                        <div class="progress-mini">
                                            <div class="bar" style="width: {{ $pct }}%; background: {{ $colors[$pp->status] ?? '#6c757d' }};"></div>
                                        </div>
                                        <span class="session-text" style="color: {{ $colors[$pp->status] ?? '#6c757d' }};">
                                            {{ $pp->sesi_terpakai }}/{{ $pp->total_sesi }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    @if ($pp->harga_bayar == 0)
                                        <span class="badge rounded-pill" style="background: #e8f5e9; color: #2e7d32; font-size: 0.78rem;">GRATIS</span>
                                    @else
                                        <span class="fw-semibold" style="color: #2e7d32;">Rp {{ number_format($pp->harga_bayar, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="date-range">
                                        {{ $pp->tanggal_mulai->format('d M Y') }}<br>
                                        s/d {{ $pp->tanggal_expired->format('d M Y') }}
                                        @if ($pp->status == 'aktif')
                                            <br><span class="sisa"><i class="ri-time-line"></i> {{ $pp->sisaHari() }} hari lagi</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="status-pill status-{{ $pp->status }}">
                                        {{ ucfirst($pp->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('paket-pemeriksaan.pasien.show', $pp->id) }}"
                                            class="btn btn-outline-primary btn-sm" title="Detail">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                        @if ($pp->status == 'aktif')
                                            <form action="{{ route('paket-pemeriksaan.pasien.cancel', $pp->id) }}"
                                                method="POST" class="d-inline form-cancel">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Batalkan">
                                                    <i class="ri-close-circle-line"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="ri-user-heart-line d-block"></i>
                                        <h6 class="text-muted">Belum ada paket pasien</h6>
                                        <p class="text-muted" style="font-size: 0.85rem;">Berikan paket pemeriksaan ke pasien untuk mulai</p>
                                        <a href="{{ route('paket-pemeriksaan.pasien.create') }}" class="btn btn-primary btn-sm mt-2">
                                            <i class="ri-user-add-line"></i> Berikan Paket
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($paketPasiens->hasPages())
                <div class="px-3 py-2 border-top">
                    {{ $paketPasiens->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.form-cancel').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: 'Batalkan Paket?',
            text: 'Paket yang dibatalkan tidak bisa diaktifkan kembali.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Tidak'
        }).then(function(result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
