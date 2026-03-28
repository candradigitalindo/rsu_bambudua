@extends('layouts.app')
@section('title', 'Detail Paket Pasien')

@push('style')
<style>
    .detail-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; color: #fff; padding: 24px; position: relative; overflow: hidden; }
    .detail-header::before { content: ''; position: absolute; top: -30px; right: -30px; width: 120px; height: 120px; background: rgba(255,255,255,0.08); border-radius: 50%; }
    .detail-header::after { content: ''; position: absolute; bottom: -40px; right: 60px; width: 80px; height: 80px; background: rgba(255,255,255,0.05); border-radius: 50%; }
    .detail-header .patient-name { font-size: 1.3rem; font-weight: 700; }
    .detail-header .patient-meta { font-size: 0.85rem; opacity: 0.85; }
    .detail-avatar { width: 56px; height: 56px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.2rem; flex-shrink: 0; }
    .status-badge-lg { padding: 6px 16px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.5px; }
    .status-aktif-lg { background: rgba(255,255,255,0.2); color: #fff; }
    .status-selesai-lg { background: #e8f5e9; color: #2e7d32; }
    .status-expired-lg { background: #fff3e0; color: #e65100; }
    .status-batal-lg { background: #fce4ec; color: #c62828; }

    .info-card { border-radius: 10px; padding: 16px; text-align: center; border: 1px solid #e9ecef; }
    .info-card .info-icon { width: 42px; height: 42px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 8px; }
    .info-card .info-value { font-size: 1.2rem; font-weight: 700; line-height: 1.2; }
    .info-card .info-label { font-size: 0.75rem; color: #6c757d; margin-top: 2px; }

    .circular-progress { position: relative; width: 120px; height: 120px; margin: 0 auto; }
    .circular-progress svg { transform: rotate(-90deg); }
    .circular-progress .progress-text { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; }
    .circular-progress .progress-value { font-size: 1.5rem; font-weight: 800; line-height: 1; }
    .circular-progress .progress-label { font-size: 0.7rem; color: #6c757d; }

    .use-session-card { border: 2px dashed #0d6efd; border-radius: 12px; padding: 20px; background: #f8f9ff; transition: all 0.2s; }
    .use-session-card:hover { background: #eef2ff; border-color: #4a80f0; }
    .sesi-number { display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 50%; background: #0d6efd; color: #fff; font-size: 1.2rem; font-weight: 700; }

    .timeline { position: relative; padding-left: 30px; }
    .timeline::before { content: ''; position: absolute; left: 14px; top: 0; bottom: 0; width: 2px; background: #e9ecef; }
    .timeline-item { position: relative; padding-bottom: 20px; }
    .timeline-item:last-child { padding-bottom: 0; }
    .timeline-item .timeline-dot { position: absolute; left: -23px; top: 4px; width: 18px; height: 18px; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 0 0 2px #0d6efd; background: #0d6efd; }
    .timeline-item .timeline-content { background: #fff; border: 1px solid #e9ecef; border-radius: 10px; padding: 12px 16px; }
    .timeline-item .timeline-date { font-size: 0.78rem; color: #6c757d; }
    .timeline-item .timeline-title { font-weight: 600; font-size: 0.9rem; }
    .timeline-item .timeline-meta { font-size: 0.8rem; color: #6c757d; }

    .empty-timeline { text-align: center; padding: 40px 20px; }
    .empty-timeline i { font-size: 3rem; color: #dee2e6; }
</style>
@endpush

@php
    $pct = $paketPasien->total_sesi > 0 ? round(($paketPasien->sesi_terpakai / $paketPasien->total_sesi) * 100) : 0;
    $sisa = $paketPasien->total_sesi - $paketPasien->sesi_terpakai;
    $statusColors = ['aktif' => '#0d6efd', 'selesai' => '#198754', 'expired' => '#e65100', 'batal' => '#dc3545'];
    $color = $statusColors[$paketPasien->status] ?? '#6c757d';
    $initials = collect(explode(' ', $paketPasien->pasien->name ?? 'X'))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->join('');
    $circumference = 2 * 3.14159 * 50;
    $dashoffset = $circumference - ($pct / 100 * $circumference);
@endphp

@section('content')
    {{-- Header --}}
    <div class="detail-header mb-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('paket-pemeriksaan.pasien.index') }}" class="btn btn-sm" style="background: rgba(255,255,255,0.15); color:#fff;">
                    <i class="ri-arrow-left-line"></i>
                </a>
                <div class="detail-avatar">{{ $initials }}</div>
                <div>
                    <div class="patient-name">{{ $paketPasien->pasien->name ?? '-' }}</div>
                    <div class="patient-meta">
                        <i class="ri-file-list-line me-1"></i>RM: {{ $paketPasien->pasien->rekam_medis ?? '-' }}
                        <span class="mx-2">|</span>
                        <i class="ri-map-pin-line me-1"></i>{{ $paketPasien->pasien->alamat ?? '-' }}
                    </div>
                </div>
            </div>
            <span class="status-badge-lg status-{{ $paketPasien->status }}-lg">
                @switch($paketPasien->status)
                    @case('aktif') <i class="ri-checkbox-circle-line me-1"></i>AKTIF @break
                    @case('selesai') <i class="ri-check-double-line me-1"></i>SELESAI @break
                    @case('expired') <i class="ri-time-line me-1"></i>EXPIRED @break
                    @case('batal') <i class="ri-close-circle-line me-1"></i>BATAL @break
                @endswitch
            </span>
        </div>
    </div>

    <div class="row gx-3">
        {{-- Left Column --}}
        <div class="col-xl-8 col-lg-8">
            {{-- Info Cards --}}
            <div class="row g-3 mb-3">
                <div class="col-md-3 col-6">
                    <div class="info-card">
                        <div class="info-icon" style="background: #e8f4fd;"><i class="ri-gift-line text-primary"></i></div>
                        <div class="info-value text-primary">{{ $paketPasien->paketPemeriksaan->name ?? '-' }}</div>
                        <div class="info-label">Nama Paket</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="info-card">
                        <div class="info-icon" style="background: #e8f5e9;"><i class="ri-money-dollar-circle-line text-success"></i></div>
                        <div class="info-value text-success">
                            @if ($paketPasien->harga_bayar == 0) GRATIS @else Rp {{ number_format($paketPasien->harga_bayar, 0, ',', '.') }} @endif
                        </div>
                        <div class="info-label">Harga</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="info-card">
                        <div class="info-icon" style="background: #fff3e0;"><i class="ri-calendar-line" style="color: #e65100;"></i></div>
                        <div class="info-value" style="font-size: 0.85rem;">
                            {{ $paketPasien->tanggal_mulai->format('d M Y') }}
                            <br><small class="text-muted">s/d {{ $paketPasien->tanggal_expired->format('d M Y') }}</small>
                        </div>
                        <div class="info-label">
                            @if ($paketPasien->status == 'aktif')
                                <span style="color: #e65100; font-weight: 600;"><i class="ri-time-line"></i> {{ $paketPasien->sisaHari() }} hari lagi</span>
                            @else
                                Periode
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="info-card">
                        <div class="info-icon" style="background: #f3e5f5;"><i class="ri-user-star-line" style="color: #7b1fa2;"></i></div>
                        <div class="info-value" style="color: #7b1fa2; font-size: 0.9rem;">{{ $paketPasien->createdBy->name ?? '-' }}</div>
                        <div class="info-label">Dibuat Oleh</div>
                    </div>
                </div>
            </div>

            @if ($paketPasien->catatan)
                <div class="alert alert-light border mb-3">
                    <i class="ri-sticky-note-line me-1 text-muted"></i>
                    <strong>Catatan:</strong> {{ $paketPasien->catatan }}
                </div>
            @endif

            {{-- Use Session --}}
            @if ($paketPasien->status == 'aktif' && $paketPasien->sesi_terpakai < $paketPasien->total_sesi)
                <div class="use-session-card mb-3">
                    <form action="{{ route('paket-pemeriksaan.pasien.use-sesi', $paketPasien->id) }}" method="POST" id="formUseSesi">
                        @csrf
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="sesi-number">{{ $paketPasien->sesi_terpakai + 1 }}</div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">Gunakan Sesi ke-{{ $paketPasien->sesi_terpakai + 1 }}</div>
                                <small class="text-muted">Sisa {{ $sisa }} sesi lagi dari {{ $paketPasien->total_sesi }} total</small>
                            </div>
                            <div class="d-flex gap-2 align-items-center flex-grow-1" style="max-width: 400px;">
                                <input type="text" class="form-control" name="catatan" placeholder="Catatan sesi (opsional)">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="ri-check-line me-1"></i> Gunakan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Timeline / Usage History --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ri-history-line me-1"></i> Riwayat Penggunaan Sesi</h5>
                </div>
                <div class="card-body">
                    @if ($paketPasien->usages->count() > 0)
                        <div class="timeline">
                            @foreach ($paketPasien->usages->sortByDesc('sesi_ke') as $usage)
                                <div class="timeline-item">
                                    <div class="timeline-dot" style="background: {{ $color }}; box-shadow: 0 0 0 2px {{ $color }};"></div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="timeline-title">
                                                    <span class="badge rounded-pill" style="background: {{ $color }};">Sesi {{ $usage->sesi_ke }}</span>
                                                    @if ($usage->catatan)
                                                        <span class="ms-2 text-muted" style="font-weight: normal; font-size: 0.85rem;">{{ $usage->catatan }}</span>
                                                    @endif
                                                </div>
                                                <div class="timeline-meta mt-1">
                                                    <i class="ri-user-line me-1"></i>{{ $usage->usedBy->name ?? '-' }}
                                                    @if ($usage->encounter)
                                                        <span class="ms-2"><i class="ri-hospital-line me-1"></i>{{ $usage->encounter->no_encounter }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="timeline-date">
                                                {{ $usage->created_at->format('d M Y') }}<br>
                                                <small>{{ $usage->created_at->format('H:i') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-timeline">
                            <i class="ri-history-line d-block mb-2"></i>
                            <h6 class="text-muted">Belum ada sesi yang digunakan</h6>
                            <p class="text-muted" style="font-size: 0.85rem;">Riwayat akan muncul setelah sesi pertama digunakan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column - Progress & Actions --}}
        <div class="col-xl-4 col-lg-4">
            <div class="card mb-3">
                <div class="card-body text-center py-4">
                    <h6 class="text-muted mb-3">Progress Sesi</h6>
                    <div class="circular-progress mb-3">
                        <svg width="120" height="120" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="50" fill="none" stroke="#e9ecef" stroke-width="10" />
                            <circle cx="60" cy="60" r="50" fill="none" stroke="{{ $color }}" stroke-width="10"
                                stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $dashoffset }}"
                                stroke-linecap="round" />
                        </svg>
                        <div class="progress-text">
                            <div class="progress-value" style="color: {{ $color }};">{{ $pct }}%</div>
                            <div class="progress-label">selesai</div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center gap-4 mb-2">
                        <div>
                            <div class="fw-bold fs-5" style="color: {{ $color }};">{{ $paketPasien->sesi_terpakai }}</div>
                            <small class="text-muted">Terpakai</small>
                        </div>
                        <div style="width: 1px; background: #e9ecef;"></div>
                        <div>
                            <div class="fw-bold fs-5">{{ $sisa }}</div>
                            <small class="text-muted">Sisa</small>
                        </div>
                        <div style="width: 1px; background: #e9ecef;"></div>
                        <div>
                            <div class="fw-bold fs-5">{{ $paketPasien->total_sesi }}</div>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="d-grid gap-2 mb-3">
                <a href="{{ route('paket-pemeriksaan.pasien.index') }}" class="btn btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i> Kembali ke Daftar
                </a>
                @if ($paketPasien->status == 'aktif')
                    <form action="{{ route('paket-pemeriksaan.pasien.cancel', $paketPasien->id) }}" method="POST" id="formCancel">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="ri-close-circle-line me-1"></i> Batalkan Paket
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#formUseSesi').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: 'Gunakan Sesi?',
            html: 'Konfirmasi penggunaan <strong>Sesi ke-{{ $paketPasien->sesi_terpakai + 1 }}</strong> dari {{ $paketPasien->total_sesi }}.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="ri-check-line"></i> Ya, Gunakan',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    $('#formCancel').on('submit', function(e) {
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
