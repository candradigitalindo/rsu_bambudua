@extends('layouts.app')
@section('title', 'Histori Pendapatan')
@push('style')
    <!-- Date Range CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/daterange/daterange.css') }}">
    <!-- Data Tables -->
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bs5.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bs5-custom.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@section('content')
    <!-- Page header starts -->
    <div class="page-header">
        <div class="toggle-sidebar" id="toggle-sidebar">
            <i class="ri-menu-line"></i>
        </div>

        <!-- Breadcrumb start -->
        <ol class="breadcrumb d-md-flex d-none">
            <li class="breadcrumb-item">
                <i class="ri-home-line"></i> <a href="{{ route('dokter.index') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Histori Pendapatan
            </li>
        </ol>
        <!-- Breadcrumb end -->
    </div>
    <!-- Page header ends -->

    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-sm-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Histori Pendapatan</h5>
                </div>
                <div class="card-body">
                    <!-- Filter tanggal -->
                    <form method="GET" action="{{ route('dokter.histori-pendapatan') }}" class="mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-filter-3-line"></i> Filter
                                </button>
                                <a href="{{ route('dokter.histori-pendapatan') }}" class="btn btn-outline-secondary">
                                    <i class="ri-refresh-line"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Statistik -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h4 class="text-success mb-1">{{ formatPrice($totalPendapatan) }}</h4>
                                    <p class="text-muted mb-0 small">Total Pendapatan</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h4 class="text-primary mb-1">{{ $totalTransaksi }}</h4>
                                    <p class="text-muted mb-0 small">Total Transaksi</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h5 class="text-success mb-1">{{ formatPrice($totalDibayar) }}</h5>
                                    <p class="text-muted mb-0 small">Sudah Dibayar</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h5 class="text-warning mb-1">{{ formatPrice($totalPending) }}</h5>
                                    <p class="text-muted mb-0 small">Pending</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Breakdown by Jenis -->
                    <div class="card border mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Breakdown Per Jenis Pendapatan</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Jenis Pendapatan</th>
                                            <th class="text-center">Jumlah Transaksi</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($breakdownByJenis as $item)
                                            <tr>
                                                <td>{{ $item['jenis'] }}</td>
                                                <td class="text-center">{{ $item['jumlah'] }}</td>
                                                <td class="text-end"><strong>{{ formatPrice($item['total']) }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-active">
                                            <th>Total</th>
                                            <th class="text-center">{{ $totalTransaksi }}</th>
                                            <th class="text-end">{{ formatPrice($totalPendapatan) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel data -->
                    <div class="table-responsive">
                        <table id="historiPendapatanTable" class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis Pendapatan</th>
                                    <th>Keterangan</th>
                                    <th class="text-end">Jumlah</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historiPendapatan as $pendapatan)
                                    <tr>
                                        <td>
                                            <strong>{{ \Carbon\Carbon::parse($pendapatan['tanggal'])->format('d/m/Y') }}</strong><br>
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($pendapatan['tanggal'])->format('H:i') }}</small>
                                        </td>
                                        <td><span
                                                class="badge bg-primary-subtle text-primary">{{ $pendapatan['jenis'] }}</span>
                                        </td>
                                        <td>{{ Str::limit($pendapatan['keterangan'], 60) }}</td>
                                        <td class="text-end"><strong
                                                class="text-success">{{ formatPrice($pendapatan['amount']) }}</strong></td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-{{ $pendapatan['status_class'] }}-subtle text-{{ $pendapatan['status_class'] }}">
                                                {{ $pendapatan['status'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <i class="ri-wallet-3-line fs-1"></i>
                                            <p class="mb-0 mt-2">Tidak ada data pendapatan pada periode ini</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Row ends -->
@endsection

@push('scripts')
    <!-- Data Tables -->
    <script src="{{ asset('vendor/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#historiPendapatanTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                },
                "pageLength": 25,
                "order": [
                    [0, "desc"]
                ] // Sort by tanggal descending
            });
        });
    </script>
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom-scrollbar.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
@endpush
