@extends('layouts.app')

@section('title', 'Laporan Pembayaran')

@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1"><i class="ri-file-chart-line"></i> Laporan Pembayaran</h2>
                <p class="text-muted">Laporan pembayaran berdasarkan rentang tanggal</p>
            </div>
            <div>
                <a href="{{ route('kasir.index') }}" class="btn btn-outline-secondary">
                    <i class="ri-arrow-left-line"></i> Kembali
                </a>
                <a href="{{ route('kasir.histori') }}" class="btn btn-outline-primary">
                    <i class="ri-history-line"></i> Histori
                </a>
                <button onclick="window.print()" class="btn btn-success">
                    <i class="ri-printer-line"></i> Cetak Laporan
                </button>
            </div>
        </div>

        {{-- Filter --}}
        <div class="card mb-4 no-print">
            <div class="card-body">
                <form method="GET" action="{{ route('kasir.laporan') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Dari</label>
                        <input type="date" class="form-control" name="tanggal_dari" value="{{ $tanggalDari }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Sampai</label>
                        <input type="date" class="form-control" name="tanggal_sampai" value="{{ $tanggalSampai }}"
                            required>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-search-line"></i> Tampilkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Total Pembayaran</p>
                                <h4 class="mb-0">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</h4>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="ri-money-dollar-circle-line text-primary" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Jumlah Transaksi</p>
                                <h4 class="mb-0">{{ number_format($jumlahTransaksi) }}</h4>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="ri-file-list-3-line text-success" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Tindakan</p>
                                <h4 class="mb-0">Rp {{ number_format($totalTindakan, 0, ',', '.') }}</h4>
                            </div>
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="ri-stethoscope-line text-info" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Resep/Obat</p>
                                <h4 class="mb-0">Rp {{ number_format($totalResep, 0, ',', '.') }}</h4>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="ri-capsule-line text-warning" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment Methods Breakdown --}}
        @if (count($byPaymentMethod) > 0)
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="ri-bank-card-line"></i> Pembayaran per Metode</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($byPaymentMethod as $method => $total)
                            <div class="col-md-3 mb-3">
                                <div class="border rounded p-3">
                                    <h6 class="text-muted mb-1">{{ $method }}</h6>
                                    <h4 class="mb-0">Rp {{ number_format($total, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Detail Table --}}
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="ri-list-check"></i> Detail Transaksi
                    <small class="text-muted">
                        ({{ \Carbon\Carbon::parse($tanggalDari)->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse($tanggalSampai)->format('d M Y') }})
                    </small>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-secondary">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>No. Encounter</th>
                                <th>Pasien</th>
                                <th>RM</th>
                                <th class="text-end">Tindakan</th>
                                <th class="text-end">Resep</th>
                                <th class="text-end">Total</th>
                                <th>Metode</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @forelse ($encounters as $enc)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>
                                        <small>{{ $enc->updated_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>{{ $enc->no_encounter }}</td>
                                    <td>{{ $enc->name_pasien }}</td>
                                    <td>{{ $enc->pasien->rekam_medis ?? '-' }}</td>
                                    <td class="text-end">
                                        @if ($enc->status_bayar_tindakan)
                                            Rp {{ number_format($enc->total_bayar_tindakan, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($enc->status_bayar_resep)
                                            Rp {{ number_format($enc->total_bayar_resep, 0, ',', '.') }}
                                        @else
                                            -
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
                                    <td>
                                        <small>
                                            @if ($enc->status_bayar_tindakan)
                                                {{ $enc->metode_pembayaran_tindakan }}
                                            @endif
                                            @if ($enc->status_bayar_resep && $enc->metode_pembayaran_resep != $enc->metode_pembayaran_tindakan)
                                                @if ($enc->status_bayar_tindakan)
                                                    /
                                                @endif
                                                {{ $enc->metode_pembayaran_resep }}
                                            @endif
                                        </small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="ri-inbox-line" style="font-size: 48px; color: #ccc;"></i>
                                        <p class="text-muted mt-2">Tidak ada data transaksi pada periode ini</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($encounters->count() > 0)
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="5" class="text-end">TOTAL</th>
                                    <th class="text-end">Rp {{ number_format($totalTindakan, 0, ',', '.') }}</th>
                                    <th class="text-end">Rp {{ number_format($totalResep, 0, ',', '.') }}</th>
                                    <th class="text-end">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            .btn {
                display: none !important;
            }
        }
    </style>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom-scrollbar.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
@endpush
