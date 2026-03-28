@extends('layouts.app')
@section('title', 'Master Paket Pemeriksaan')

@push('style')
<style>
    .stat-card { background: #fff; border-radius: 10px; padding: 20px; border-left: 4px solid; transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
    .stat-card .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
    .stat-card .stat-value { font-size: 1.6rem; font-weight: 700; line-height: 1.2; }
    .stat-card .stat-label { font-size: 0.8rem; color: #6c757d; margin-top: 2px; }
    .paket-row { transition: background-color 0.15s; }
    .paket-row:hover { background-color: #f8f9ff; }
    .paket-badge-sesi { display: inline-flex; align-items: center; gap: 4px; background: #e8f4fd; color: #0d6efd; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
    .paket-badge-hari { display: inline-flex; align-items: center; gap: 4px; background: #fff3e0; color: #e65100; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
    .paket-price { font-size: 0.95rem; font-weight: 700; color: #2e7d32; }
    .paket-free { background: linear-gradient(135deg, #e8f5e9, #c8e6c9); color: #2e7d32; padding: 3px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; }
    .subscriber-count { display: inline-flex; align-items: center; gap: 3px; font-size: 0.78rem; color: #6c757d; }
    .subscriber-count i { font-size: 0.85rem; }
    .empty-state { padding: 60px 20px; text-align: center; }
    .empty-state i { font-size: 4rem; color: #dee2e6; margin-bottom: 16px; }
    .empty-state h6 { color: #6c757d; margin-bottom: 8px; }
    .empty-state p { color: #adb5bd; font-size: 0.85rem; }
</style>
@endpush

@section('content')
    {{-- Stats Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-3 col-md-6 col-6">
            <div class="stat-card" style="border-color: #0d6efd;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value text-primary">{{ $stats['total'] }}</div>
                        <div class="stat-label">Total Paket</div>
                    </div>
                    <div class="stat-icon" style="background: #e8f4fd;">
                        <i class="ri-gift-line text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6">
            <div class="stat-card" style="border-color: #198754;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value text-success">{{ $stats['aktif'] }}</div>
                        <div class="stat-label">Paket Aktif</div>
                    </div>
                    <div class="stat-icon" style="background: #e8f5e9;">
                        <i class="ri-checkbox-circle-line text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6">
            <div class="stat-card" style="border-color: #6c757d;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value text-secondary">{{ $stats['nonaktif'] }}</div>
                        <div class="stat-label">Nonaktif</div>
                    </div>
                    <div class="stat-icon" style="background: #f1f3f5;">
                        <i class="ri-pause-circle-line text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6">
            <div class="stat-card" style="border-color: #20c997;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value" style="color: #20c997;">{{ $stats['gratis'] }}</div>
                        <div class="stat-label">Paket Gratis</div>
                    </div>
                    <div class="stat-icon" style="background: #e6fcf5;">
                        <i class="ri-hand-heart-line" style="color: #20c997;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="row gx-3">
        <div class="col-xxl-12 col-sm-12">
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h5 class="card-title mb-0">
                            <i class="ri-gift-line me-1"></i> Master Paket Pemeriksaan
                        </h5>
                        <div class="d-flex gap-2 align-items-center">
                            <div class="search-container">
                                <form method="GET" action="{{ route('paket-pemeriksaan.index') }}">
                                    <input type="text" class="form-control" name="name"
                                        placeholder="Cari paket..." value="{{ request('name') }}">
                                    <i class="ri-search-line"></i>
                                </form>
                            </div>
                            <a href="{{ route('paket-pemeriksaan.create') }}" class="btn btn-primary">
                                <i class="ri-add-line"></i>
                                <span class="btn-text">Tambah Paket</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 280px;">Paket</th>
                                    <th class="text-center" style="width: 100px;">Sesi</th>
                                    <th style="width: 160px;">Harga</th>
                                    <th class="text-center" style="width: 120px;">Masa Berlaku</th>
                                    <th class="text-center" style="width: 100px;">Pengguna</th>
                                    <th class="text-center" style="width: 90px;">Status</th>
                                    <th class="text-center" style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pakets as $paket)
                                    <tr class="paket-row">
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="d-flex align-items-center justify-content-center rounded-3"
                                                     style="width:42px; height:42px; background: {{ $paket->is_gratis ? 'linear-gradient(135deg, #e8f5e9, #c8e6c9)' : 'linear-gradient(135deg, #e3f2fd, #bbdefb)' }}; flex-shrink:0;">
                                                    <i class="ri-gift-line fs-5" style="color: {{ $paket->is_gratis ? '#2e7d32' : '#1565c0' }};"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $paket->name }}</div>
                                                    @if ($paket->description)
                                                        <small class="text-muted">{{ Str::limit($paket->description, 60) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="paket-badge-sesi">
                                                <i class="ri-play-circle-line"></i> {{ $paket->jumlah_sesi }}x
                                            </span>
                                        </td>
                                        <td>
                                            @if ($paket->is_gratis)
                                                <span class="paket-free">
                                                    <i class="ri-hand-heart-line"></i> GRATIS
                                                </span>
                                            @else
                                                <span class="paket-price">Rp {{ number_format($paket->harga, 0, ',', '.') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="paket-badge-hari">
                                                <i class="ri-calendar-line"></i> {{ $paket->masa_berlaku_hari }} hr
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="subscriber-count">
                                                <i class="ri-group-line"></i> {{ $paket->paket_pasiens_count }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if ($paket->status)
                                                <span class="badge bg-success-subtle text-success rounded-pill px-3">Aktif</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning rounded-pill px-3">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('paket-pemeriksaan.edit', $paket->id) }}"
                                                    class="btn btn-outline-primary btn-sm" title="Edit Paket">
                                                    <i class="ri-edit-2-line"></i>
                                                </a>
                                                <form action="{{ route('paket-pemeriksaan.destroy', $paket->id) }}"
                                                    method="POST" class="d-inline form-delete">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus Paket">
                                                        <i class="ri-delete-bin-5-line"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty-state">
                                                <i class="ri-gift-line d-block"></i>
                                                <h6>Belum ada paket pemeriksaan</h6>
                                                <p>Mulai buat paket pemeriksaan untuk pasien Anda</p>
                                                <a href="{{ route('paket-pemeriksaan.create') }}" class="btn btn-primary btn-sm mt-2">
                                                    <i class="ri-add-line"></i> Buat Paket Pertama
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($pakets->hasPages())
                        <div class="px-3 py-2 border-top">
                            {{ $pakets->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.form-delete').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: 'Hapus Paket?',
            text: 'Data paket yang sudah dihapus tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
