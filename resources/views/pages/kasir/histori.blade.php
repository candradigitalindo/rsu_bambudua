@extends('layouts.app')

@section('title', 'Histori Transaksi Pembayaran')

@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1"><i class="ri-history-line"></i> Histori Transaksi Pembayaran</h2>
                <p class="text-muted">Daftar riwayat pembayaran yang sudah lunas</p>
            </div>
            <div>
                <a href="{{ route('kasir.index') }}" class="btn btn-outline-secondary">
                    <i class="ri-arrow-left-line"></i> Kembali
                </a>
                <a href="{{ route('kasir.laporan') }}" class="btn btn-primary">
                    <i class="ri-file-chart-line"></i> Laporan
                </a>
            </div>
        </div>

        {{-- Filter --}}
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('kasir.histori') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Dari</label>
                        <input type="date" class="form-control" name="tanggal_dari"
                            value="{{ request('tanggal_dari') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Sampai</label>
                        <input type="date" class="form-control" name="tanggal_sampai"
                            value="{{ request('tanggal_sampai') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cari Pasien / No. Encounter</label>
                        <input type="text" class="form-control" name="search" placeholder="Nama, RM, atau No. Encounter"
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="ri-search-line"></i> Filter
                        </button>
                        <a href="{{ route('kasir.histori') }}" class="btn btn-outline-secondary">
                            <i class="ri-refresh-line"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>No. Encounter</th>
                                <th>Pasien</th>
                                <th>No. RM</th>
                                <th>Tindakan</th>
                                <th>Resep</th>
                                <th>Total</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($encounters as $enc)
                                <tr>
                                    <td>
                                        <small>{{ $enc->updated_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $enc->no_encounter }}</strong>
                                    </td>
                                    <td>{{ $enc->name_pasien }}</td>
                                    <td>{{ $enc->pasien->rekam_medis ?? '-' }}</td>
                                    <td class="text-end">
                                        @if ($enc->status_bayar_tindakan)
                                            <span class="badge bg-success">Lunas</span>
                                            <br>
                                            <small>Rp {{ number_format($enc->total_bayar_tindakan, 0, ',', '.') }}</small>
                                            <br>
                                            <small class="text-muted">{{ $enc->metode_pembayaran_tindakan }}</small>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($enc->status_bayar_resep)
                                            <span class="badge bg-success">Lunas</span>
                                            <br>
                                            <small>Rp {{ number_format($enc->total_bayar_resep, 0, ',', '.') }}</small>
                                            <br>
                                            <small class="text-muted">{{ $enc->metode_pembayaran_resep }}</small>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <strong>Rp
                                            {{ number_format(
                                                ($enc->status_bayar_tindakan ? $enc->total_bayar_tindakan : 0) +
                                                    ($enc->status_bayar_resep ? $enc->total_bayar_resep : 0),
                                                0,
                                                ',',
                                                '.',
                                            ) }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('kasir.cetakStruk', $enc->id) }}"
                                            class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="ri-printer-line"></i> Cetak
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="ri-inbox-line" style="font-size: 48px; color: #ccc;"></i>
                                        <p class="text-muted mt-2">Tidak ada data transaksi</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $encounters->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom-scrollbar.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
@endpush
