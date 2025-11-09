@extends('layouts.app')
@section('title', 'Histori Pasien yang Ditangani')
@push('style')
    <!-- Date Range CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/daterange/daterange.css') }}">
    <!-- Data Tables -->
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bs5.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bs5-custom.css') }}">
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
                Histori Pasien
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
                    <h5 class="card-title">Histori Pasien yang Ditangani</h5>
                </div>
                <div class="card-body">
                    <!-- Filter tanggal -->
                    <form method="GET" action="{{ route('dokter.histori-pasien') }}" class="mb-4">
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
                                <a href="{{ route('dokter.histori-pasien') }}" class="btn btn-outline-secondary">
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
                                    <h3 class="text-primary mb-1">{{ $totalPasien }}</h3>
                                    <p class="text-muted mb-0 small">Total Pasien</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h3 class="text-primary mb-1">{{ $totalRawatJalan }}</h3>
                                    <p class="text-muted mb-0 small">Rawat Jalan</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h3 class="text-info mb-1">{{ $totalRawatInap }}</h3>
                                    <p class="text-muted mb-0 small">Rawat Inap</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h3 class="text-danger mb-1">{{ $totalIGD }}</h3>
                                    <p class="text-muted mb-0 small">IGD</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel data -->
                    <div class="table-responsive">
                        <table id="historiPasienTable" class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal & Waktu</th>
                                    <th>No. Encounter</th>
                                    <th>Rekam Medis</th>
                                    <th>Nama Pasien</th>
                                    <th>Jenis Layanan</th>
                                    <th>Diagnosis</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historiPasien as $index => $pasien)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ \Carbon\Carbon::parse($pasien['tanggal'])->format('d/m/Y') }}</strong><br>
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($pasien['tanggal'])->format('H:i') }}</small>
                                        </td>
                                        <td><span
                                                class="badge bg-secondary-subtle text-secondary">{{ $pasien['no_encounter'] }}</span>
                                        </td>
                                        <td><strong>{{ $pasien['rekam_medis'] }}</strong></td>
                                        <td>{{ $pasien['nama_pasien'] }}</td>
                                        <td>
                                            <span
                                                class="badge
                                            @if ($pasien['type'] == 1) bg-primary-subtle text-primary
                                            @elseif($pasien['type'] == 2) bg-info-subtle text-info
                                            @else bg-danger-subtle text-danger @endif">
                                                {{ $pasien['type_text'] }}
                                            </span>
                                        </td>
                                        <td>{{ Str::limit($pasien['diagnosis'], 50) }}</td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success">
                                                {{ $pasien['status'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-5">
                                            <i class="ri-folder-open-line fs-1"></i>
                                            <p class="mb-0 mt-2">Tidak ada data pasien pada periode ini</p>
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
            $('#historiPasienTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                },
                "pageLength": 25,
                "order": [
                    [1, "desc"]
                ], // Sort by tanggal descending
                "columnDefs": [{
                        "orderable": false,
                        "targets": 0
                    } // No column tidak bisa disorting
                ]
            });
        });
    </script>
@endpush
