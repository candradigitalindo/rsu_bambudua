@extends('layouts.app')

@section('title', 'Dashboard Radiologi')
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
    <div class="container-fluid px-4 py-4">
        <!-- Header Card -->
        <div class="page-header-card mb-4">
            <h4 class="mb-1 fw-bold">
                <i class="ri-hospital-line me-2"></i>
                Dashboard Radiologi
            </h4>
            <p class="text-muted mb-0">Monitoring dan statistik layanan radiologi</p>
        </div>

        <!-- Statistik Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card border-primary">
                    <div class="stat-content">
                        <div class="stat-info">
                            <div class="stat-label">Permintaan Hari Ini</div>
                            <div class="stat-value text-primary">{{ $stats['today'] ?? 0 }}</div>
                        </div>
                        <div class="stat-icon bg-primary">
                            <i class="ri-calendar-check-line"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card border-warning">
                    <div class="stat-content">
                        <div class="stat-info">
                            <div class="stat-label">Diproses</div>
                            <div class="stat-value text-warning">{{ $stats['processing'] ?? 0 }}</div>
                        </div>
                        <div class="stat-icon bg-warning">
                            <i class="ri-time-line"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card border-success">
                    <div class="stat-content">
                        <div class="stat-info">
                            <div class="stat-label">Selesai</div>
                            <div class="stat-value text-success">{{ $stats['completed'] ?? 0 }}</div>
                        </div>
                        <div class="stat-icon bg-success">
                            <i class="ri-checkbox-circle-line"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card border-danger">
                    <div class="stat-content">
                        <div class="stat-info">
                            <div class="stat-label">Tertunda</div>
                            <div class="stat-value text-danger">{{ $stats['requested'] ?? 0 }}</div>
                        </div>
                        <div class="stat-icon bg-danger">
                            <i class="ri-error-warning-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Stok -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <a href="{{ route('radiologi.supplies.index', ['filter' => 'habis']) }}" class="text-decoration-none">
                    <div class="stat-card border-danger clickable">
                        <div class="stat-content">
                            <div class="stat-info">
                                <div class="stat-label">Stok Habis</div>
                                <div class="stat-value text-danger">{{ $supplyStats['habis'] ?? 0 }}</div>
                                <small class="text-muted">Klik untuk lihat detail</small>
                            </div>
                            <div class="stat-icon bg-danger">
                                <i class="ri-alert-line"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6">
                <a href="{{ route('radiologi.supplies.index', ['filter' => 'kadaluarsa']) }}" class="text-decoration-none">
                    <div class="stat-card border-warning clickable">
                        <div class="stat-content">
                            <div class="stat-info">
                                <div class="stat-label">Bahan Kadaluarsa</div>
                                <div class="stat-value text-warning">{{ $supplyStats['kadaluarsa'] ?? 0 }}</div>
                                <small class="text-muted">Klik untuk lihat detail</small>
                            </div>
                            <div class="stat-icon bg-warning">
                                <i class="ri-error-warning-line"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Permintaan Terbaru -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="ri-file-list-3-line me-2"></i>
                    Permintaan Terbaru
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Tanggal</th>
                            <th style="width: 25%;">Pasien</th>
                            <th style="width: 25%;">Pemeriksaan</th>
                            <th style="width: 20%;">Dokter</th>
                            <th style="width: 15%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent ?? [] as $r)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ optional($r->created_at)->format('d M Y') }}</div>
                                    <small class="text-muted">{{ optional($r->created_at)->format('H:i') }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $r->pasien->name ?? '-' }}</div>
                                    <small class="text-muted">{{ $r->pasien->rekam_medis ?? '' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ optional($r->jenis)->name }}</span>
                                </td>
                                <td>{{ optional($r->dokter)->name ?? '-' }}</td>
                                <td>
                                    @php
                                        $statusColor = match ($r->status) {
                                            'requested' => 'danger',
                                            'processing' => 'warning',
                                            'completed' => 'success',
                                            default => 'primary',
                                        };
                                        $statusIcon = match ($r->status) {
                                            'requested' => 'ri-time-line',
                                            'processing' => 'ri-loader-4-line',
                                            'completed' => 'ri-checkbox-circle-line',
                                            default => 'ri-file-line',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}">
                                        <i class="{{ $statusIcon }}"></i>
                                        {{ ucfirst($r->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="ri-file-list-3-line fs-1 text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">Belum ada data permintaan</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .page-header-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
            border-left: 5px solid #10b981;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            border-left: 5px solid;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
        }

        .stat-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-info {
            flex: 1;
        }

        .stat-label {
            font-size: 0.875rem;
            color: #6B7280;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            color: white;
            opacity: 0.9;
        }

        .stat-card.clickable {
            cursor: pointer;
        }

        .stat-card.clickable:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .stat-card.clickable:active {
            transform: translateY(-2px);
        }

        .table-modern {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-modern thead {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .table-modern thead th {
            border: none;
            padding: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .table-modern thead th:first-child {
            border-top-left-radius: 12px;
        }

        .table-modern thead th:last-child {
            border-top-right-radius: 12px;
        }

        .table-modern tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f3f4f6;
        }

        .table-modern tbody tr:last-child td {
            border-bottom: none;
        }

        .table-modern tbody tr {
            transition: background-color 0.2s ease;
        }

        .table-modern tbody tr:hover {
            background-color: #f0fdf4;
        }
    </style>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
@endpush
