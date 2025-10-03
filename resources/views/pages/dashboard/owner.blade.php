@extends('layouts.app')
@section('title')
    Dashboard Owner
@endsection
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <style>
        /* Real-time Dashboard Styles */
        .ri-loader-4-line {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .progress-bar {
            transition: width 0.3s ease-in-out;
        }

        #refresh-dashboard {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        #refresh-dashboard:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .real-time-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #28a745;
            border-radius: 50%;
            animation: pulse 2s infinite;
            margin-right: 5px;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                opacity: 1;
            }
        }

        .status-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .status-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Update flash animation */
        .updated-flash {
            animation: flash 0.5s ease-in-out;
        }

        @keyframes flash {
            0% {
                background-color: transparent;
            }

            50% {
                background-color: rgba(40, 167, 69, 0.2);
            }

            100% {
                background-color: transparent;
            }
        }

        /* Department Performance Cards */
        .department-card {
            transition: all 0.3s ease;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
        }

        .department-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .department-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        .icon-box {
            transition: all 0.3s ease;
        }

        .department-card:hover .icon-box {
            transform: scale(1.1) rotate(5deg);
        }

        .department-summary {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }

        /* Icon Loading Status */
        .icon-loaded {
            opacity: 1;
            animation: iconFadeIn 0.3s ease-in;
        }

        .fallback-icon {
            font-size: 14px !important;
            display: inline-block;
            margin-right: 4px;
        }

        @keyframes iconFadeIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
@endpush
@section('content')
    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-12">
            <div class="alert alert-info">
                <p class="m-0">Menampilkan Laporan Keuangan untuk Bulan:
                    <strong>{{ now()->translatedFormat('F Y') }}</strong>
                </p>
            </div>
            <!-- Row starts -->
            <div class="row gx-3">
                <div class="col-sm-4 col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="p-2 border border-primary rounded-circle me-3">
                                    <div class="icon-box md bg-primary-lighten rounded-5">
                                        <i class="ri-cash-line fs-4 text-primary"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <h3 class="lh-1">{{ formatPrice($pendapatanTindakanDanLainnya) }}</h3>
                                    <p class="m-0">Pendapatan Jasa & Lainnya</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="p-2 border border-info rounded-circle me-3">
                                    <div class="icon-box md bg-info-lighten rounded-5">
                                        <i class="ri-medicine-bottle-line fs-4 text-info"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <h3 class="lh-1">{{ formatPrice($pendapatanFarmasiBulanIni) }}</h3>
                                    <p class="m-0">Pendapatan Farmasi</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="p-2 border border-danger rounded-circle me-3">
                                    <div class="icon-box md bg-danger-lighten rounded-5">
                                        <i class="ri-arrow-down-circle-line fs-4 text-danger"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <h3 class="lh-1">{{ formatPrice($totalPengeluaranBulanIni) }}</h3>
                                    <p class="m-0">Total Pengeluaran</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row ends -->
            <!-- Row starts -->
            <div class="row gx-3">
                <div class="col-sm-4 col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="p-2 border border-success rounded-circle me-3">
                                    <div class="icon-box md bg-success-lighten rounded-5">
                                        <i class="ri-wallet-line fs-4 text-success"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <h3 class="lh-1">{{ formatPrice($labaRugiBulanIni) }}</h3>
                                    <p class="m-0">Laba / Rugi</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="p-2 border border-info rounded-circle me-3">
                                    <div class="icon-box md bg-info-lighten rounded-5">
                                        <i class="ri-user-heart-line fs-4 text-info"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <h3 class="lh-1">{{ $totalPasienBulanIni }}</h3>
                                    <p class="m-0">Total Pasien</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="p-2 border border-primary rounded-circle me-3">
                                    <div class="icon-box md bg-primary-lighten rounded-5">
                                        <i class="ri-wallet-3-line fs-4 text-primary"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <h3 class="lh-1">{{ formatPrice($totalPendapatanBulanIni) }}</h3>
                                    <p class="m-0">Total Pendapatan</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row ends -->
        </div>
    </div>
    <!-- Row ends -->

    <!-- Row starts - Key Performance Indicators -->
    <div class="row gx-3 mt-4">
        <div class="col-12">
            <h5 class="mb-3"><i class="ri-dashboard-line"></i> Key Performance Indicators (KPI)</h5>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card mb-3 border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="text-primary">{{ $kpiData['occupancy_rate'] }}%</h4>
                            <p class="m-0 small">Bed Occupancy Rate</p>
                            <small class="text-muted">Target: 75-85%</small>
                        </div>
                        <div class="text-primary">
                            <i class="ri-hotel-bed-line fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card mb-3 border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="text-success">{{ $kpiData['avg_los'] }} hari</h4>
                            <p class="m-0 small">Average Length of Stay</p>
                            <small class="text-muted">Target: &lt; 6 hari</small>
                        </div>
                        <div class="text-success">
                            <i class="ri-time-line fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card mb-3 border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="text-info">{{ $kpiData['patient_satisfaction'] }}%</h4>
                            <p class="m-0 small">Patient Satisfaction</p>
                            <small class="text-muted">Target: &gt; 90%</small>
                        </div>
                        <div class="text-info">
                            <i class="ri-emotion-happy-line fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card mb-3 border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="text-warning">{{ $kpiData['revenue_per_patient'] }}</h4>
                            <p class="m-0 small">Revenue per Patient</p>
                            <small class="text-muted">Vs. Last Month: {{ $kpiData['revenue_growth'] }}%</small>
                        </div>
                        <div class="text-warning">
                            <i class="ri-money-dollar-circle-line fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Row ends -->

    <!-- Row starts - Bed Management Summary -->
    <div class="row gx-3 mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="ri-hotel-bed-line"></i> Status Bed Rumah Sakit</h5>

            </div>
        </div>

        <!-- Simple Bed Summary Cards -->
        <div class="col-lg-4 col-md-6 col-12">
            <div class="card mb-3 border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="text-primary mb-1">{{ $bedAnalytics['summary']['total_beds'] }}</h3>
                            <p class="m-0 small">Total Bed</p>
                            <small class="text-muted">Kapasitas rumah sakit</small>
                        </div>
                        <div class="text-primary">
                            <i class="ri-hotel-bed-fill fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-12">
            <div class="card mb-3 border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="text-success mb-1">{{ $bedAnalytics['summary']['available_beds'] }}</h3>
                            <p class="m-0 small">Bed Kosong</p>
                            <small class="text-muted">Siap untuk pasien baru</small>
                        </div>
                        <div class="text-success">
                            <i class="ri-check-double-line fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-12">
            <div class="card mb-3 border-start border-warning border-4" style="cursor: pointer;"
                onclick="showOccupiedBedsModal()">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="text-warning mb-1">{{ $bedAnalytics['summary']['occupied_beds'] }}</h3>
                            <p class="m-0 small">Bed Terpakai</p>
                            <small class="text-muted">Klik untuk lihat detail pasien</small>
                        </div>
                        <div class="text-warning">
                            <i class="ri-user-3-line fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Row ends -->

    <!-- Row starts - Real-time Operational Status -->
    <div class="row gx-3 mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="ri-pulse-line"></i> Status Operasional Real-time
                    <span class="real-time-indicator"></span>
                </h5>
                <button class="btn btn-sm btn-outline-primary" id="refresh-dashboard" title="Refresh Data Real-time">
                    <i class="ri-refresh-line"></i><span class="btn-text"> Refresh</span>
                </button>
            </div>
        </div>
        <div class="col-lg-8 col-12">
            <div class="row gx-3">
                <div class="col-lg-6 col-12">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="card-title">Kapasitas Ruangan Hari Ini</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small">
                                        <i class="ri-first-aid-kit-line me-1 text-danger" title="IGD Icon"></i>
                                        IGD
                                    </span>
                                    <span class="small"
                                        id="igd-count">{{ $operationalStatus['igd']['occupied'] }}/{{ $operationalStatus['igd']['total'] }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-danger" id="igd-progress" role="progressbar"
                                        style="width: {{ $operationalStatus['igd']['total'] > 0 ? ($operationalStatus['igd']['occupied'] / $operationalStatus['igd']['total']) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small"><i class="ri-hotel-bed-line me-1 text-primary"></i>Rawat
                                        Inap</span>
                                    <span class="small"
                                        id="inpatient-count">{{ $operationalStatus['inpatient']['occupied'] }}/{{ $operationalStatus['inpatient']['total'] }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" id="inpatient-progress" role="progressbar"
                                        style="width: {{ $operationalStatus['inpatient']['total'] > 0 ? ($operationalStatus['inpatient']['occupied'] / $operationalStatus['inpatient']['total']) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small"><i class="ri-walk-line me-1 text-success"></i>Rawat Jalan</span>
                                    <span class="small"
                                        id="outpatient-count">{{ $operationalStatus['outpatient']['today'] }}
                                        kunjungan</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" id="outpatient-progress" role="progressbar"
                                        style="width: {{ $operationalStatus['outpatient']['capacity'] > 0 ? min(($operationalStatus['outpatient']['today'] / $operationalStatus['outpatient']['capacity']) * 100, 100) : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="card-title">Status Staf Hari Ini</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h4 class="text-primary" id="doctors-count">
                                            {{ $operationalStatus['staff']['doctors_on_duty'] }}</h4>
                                        <small class="text-muted">Dokter Jaga</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-info" id="nurses-count">
                                        {{ $operationalStatus['staff']['nurses_on_duty'] }}</h4>
                                    <small class="text-muted">Perawat Jaga</small>
                                </div>
                            </div>
                            <hr>
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h5 class="text-success" id="pharmacy-count">
                                            {{ $operationalStatus['pharmacy']['active'] }}</h5>
                                        <small class="text-muted">Apotek Aktif</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h5 class="text-warning" id="lab-queue-count">
                                        {{ $operationalStatus['lab']['queue'] }}</h5>
                                    <small class="text-muted">Antrian Lab</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="card-title">Alert & Notifikasi</h6>
                </div>
                <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                    @forelse($alerts as $alert)
                        <div class="alert alert-{{ $alert['type'] }} alert-dismissible p-2 mb-2">
                            <small>
                                <i class="ri-{{ $alert['icon'] }}"></i>
                                <strong>{{ $alert['title'] }}:</strong>
                                {{ $alert['message'] }}
                            </small>
                        </div>
                    @empty
                        <div class="text-center text-muted">
                            <i class="ri-check-double-line fs-3"></i>
                            <p class="mb-0 small">Semua sistem normal</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <!-- Row ends -->

    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-lg-8 col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Grafik Keuangan Tahun {{ date('Y') }}</h5>
                </div>
                <div class="card-body pt-0">
                    <div id="grafikTahunan"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Berita Terbaru</h5>
                </div>
                <div class="card-body" style="height: 400px; overflow-y: auto;">
                    <div>
                        @forelse ($beritaTerbaru as $berita)
                            <div class="d-flex align-items-start mb-4">

                                <div class="flex-grow-1">
                                    <h6 class="m-0">{{ $berita->judul }}</h6>
                                    <p class="small m-0 text-muted">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($berita->konten), 70) }}
                                    </p>
                                    <p class="small m-0 text-muted">
                                        <i class="ri-time-line"></i>
                                        {{ $berita->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center">
                                <p>Belum ada berita yang dipublikasikan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Row ends -->
    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-lg-6 col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Grafik Kunjungan Harian (Bulan {{ now()->translatedFormat('F') }})</h5>
                </div>
                <div class="card-body pt-0">
                    <div id="grafikKunjunganHarian"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Grafik Kunjungan Bulanan (Tahun {{ date('Y') }})</h5>
                </div>
                <div class="card-body pt-0">
                    <div id="grafikKunjunganBulanan"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Row ends -->

    <!-- Row starts - Analytics & Insights -->
    <div class="row gx-3 mt-4">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Top 10 Diagnosa Bulan Ini</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ICD-10</th>
                                    <th>Diagnosa</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topDiagnoses as $index => $diagnosis)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><small>{{ $diagnosis['icd_code'] }}</small></td>
                                        <td><small>{{ Str::limit($diagnosis['description'], 30) }}</small></td>
                                        <td class="text-center"><span
                                                class="badge bg-primary">{{ $diagnosis['count'] }}</span></td>
                                        <td class="text-center">
                                            <small>{{ number_format($diagnosis['percentage'], 1) }}%</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Row ends -->

    <!-- Row starts - Department Performance -->
    <div class="row gx-3 mt-4">
        <div class="col-lg-8 col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">
                                <i class="ri-bar-chart-line me-2"></i>Performa Departemen Bulan Ini
                            </h5>
                            <small class="text-muted">
                                Analisa performance dan revenue per departemen - {{ now()->translatedFormat('F Y') }}
                            </small>
                        </div>
                        <div class="text-end">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <small class="text-muted d-block">Best Performer</small>
                                    <span
                                        class="badge bg-success">{{ $departmentPerformance->sortByDesc('revenue')->first()['name'] ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Total Departments</small>
                                    <span class="badge bg-primary">{{ $departmentPerformance->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($departmentPerformance as $dept)
                            <div class="col-lg-3 col-md-6 col-12 mb-3">
                                <div class="card border-start border-{{ $dept['color'] }} border-3 h-100 department-card"
                                    title="{{ $dept['name'] }}: {{ $dept['patients'] }} pasien, Revenue {{ formatPrice($dept['revenue']) }}"
                                    data-bs-toggle="tooltip" data-bs-placement="top">
                                    <div class="card-body text-center p-3">
                                        <div class="mb-2">
                                            <div class="icon-box lg bg-{{ $dept['color'] }}-lighten rounded-circle mx-auto d-flex align-items-center justify-content-center"
                                                style="width: 60px; height: 60px;">
                                                <i class="{{ $dept['icon'] }} fs-3 text-{{ $dept['color'] }}"></i>
                                            </div>
                                        </div>
                                        <h6 class="mb-2 text-{{ $dept['color'] }}">{{ $dept['name'] }}</h6>

                                        <!-- Metrics Grid -->
                                        <div class="row text-center mb-2">
                                            <div class="col-6">
                                                <h5 class="text-{{ $dept['color'] }} mb-0">{{ $dept['patients'] }}</h5>
                                                <small class="text-muted">Pasien</small>
                                            </div>
                                            <div class="col-6">
                                                <h5 class="text-success mb-0">{{ formatPrice($dept['revenue']) }}</h5>
                                                <small class="text-muted">Revenue</small>
                                            </div>
                                        </div>

                                        <!-- Revenue per Patient -->
                                        <div class="mb-2">
                                            <small class="text-muted">Avg per Pasien:</small>
                                            <span class="fw-bold text-info">
                                                {{ $dept['patients'] > 0 ? formatPrice($dept['revenue'] / $dept['patients']) : 'Rp 0' }}
                                            </span>
                                        </div>

                                        <!-- Performance Bar -->
                                        <div class="progress mt-2" style="height: 8px;" title="Perbandingan Revenue">
                                            <div class="progress-bar bg-{{ $dept['color'] }}" role="progressbar"
                                                style="width: {{ $departmentPerformance->max('revenue') > 0 ? ($dept['revenue'] / $departmentPerformance->max('revenue')) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                        <small class="text-muted">Performance vs Top Department</small>

                                        <!-- Department Status Badge -->
                                        @if ($dept['patients'] > 0)
                                            <span class="badge bg-success mt-2">Aktif</span>
                                        @else
                                            <span class="badge bg-warning mt-2">Tidak Aktif</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Summary Row -->
                    <div class="row mt-3 pt-3 department-summary">
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="text-center">
                                <h4 class="text-primary">{{ $departmentPerformance->sum('patients') }}</h4>
                                <small class="text-muted">Total Pasien Bulan Ini</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="text-center">
                                <h4 class="text-success">{{ formatPrice($departmentPerformance->sum('revenue')) }}</h4>
                                <small class="text-muted">Total Revenue</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="text-center">
                                <h4 class="text-info">
                                    {{ $departmentPerformance->sum('patients') > 0 ? formatPrice($departmentPerformance->sum('revenue') / $departmentPerformance->sum('patients')) : 'Rp 0' }}
                                </h4>
                                <small class="text-muted">Avg Revenue per Pasien</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="text-center">
                                <h4 class="text-warning">
                                    {{ $departmentPerformance->where('patients', '>', 0)->count() }}/{{ $departmentPerformance->count() }}
                                </h4>
                                <small class="text-muted">Departemen Aktif</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Inventory Alerts</h5>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    @forelse($inventoryAlerts as $item)
                        <div
                            class="d-flex justify-content-between align-items-center mb-2 p-2 border-start border-{{ $item['level'] === 'critical' ? 'danger' : 'warning' }} border-3">
                            <div>
                                <small class="fw-bold">{{ $item['name'] }}</small><br>
                                <small class="text-muted">Sisa: {{ $item['current_stock'] }} {{ $item['unit'] }}</small>
                            </div>
                            <span
                                class="badge bg-{{ $item['level'] === 'critical' ? 'danger' : 'warning' }}">{{ ucfirst($item['level']) }}</span>
                        </div>
                    @empty
                        <div class="text-center text-muted">
                            <i class="ri-check-line fs-3"></i>
                            <p class="mb-0 small">Semua stok aman</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <!-- Row ends -->

    <!-- Row starts - Financial Summary -->
    <div class="row gx-3 mt-4">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Executive Summary - Financial Health</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="text-center">
                                <h3 class="text-{{ $financialHealth['cash_flow'] >= 0 ? 'success' : 'danger' }}">
                                    {{ formatPrice($financialHealth['cash_flow']) }}</h3>
                                <p class="m-0 small">Net Cash Flow</p>
                                <small class="text-muted">Bulan ini</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="text-center">
                                <h3 class="text-primary">{{ $financialHealth['profit_margin'] }}%</h3>
                                <p class="m-0 small">Profit Margin</p>
                                <small class="text-muted">Target: > 15%</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="text-center">
                                <h3 class="text-info">{{ $financialHealth['operating_ratio'] }}%</h3>
                                <p class="m-0 small">Operating Ratio</p>
                                <small class="text-muted">Target: < 85%</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="text-center">
                                <h3 class="text-warning">{{ $financialHealth['days_in_ar'] }} hari</h3>
                                <p class="m-0 small">Days in A/R</p>
                                <small class="text-muted">Target: < 30 hari</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Row ends -->

    <!-- Modal untuk Data Pasien Bed Terpakai -->
    <div class="modal fade" id="occupiedBedsModal" tabindex="-1" aria-labelledby="occupiedBedsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="occupiedBedsModalLabel">
                        <i class="ri-user-3-line me-2"></i>
                        Data Pasien Bed Terpakai ({{ $bedAnalytics['summary']['occupied_beds'] }} Pasien)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="occupiedBedsContent">
                        <!-- Content will be loaded via AJAX -->
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Memuat data pasien...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-info" onclick="testBedLogic()">
                        <i class="ri-bug-line me-1"></i> Test Logic
                    </button>
                    <button type="button" class="btn btn-warning" onclick="cleanupAdmissionData()">
                        <i class="ri-tools-line me-1"></i> Cleanup Data
                    </button>
                    <button type="button" class="btn btn-success" onclick="testKPIAccuracy()">
                        <i class="ri-bar-chart-line me-1"></i> Test KPI
                    </button>
                    <button type="button" class="btn btn-primary" onclick="refreshOccupiedBeds()">
                        <i class="ri-refresh-line me-1"></i> Refresh Data
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>

    <!-- Apex Charts -->
    <script src="{{ asset('vendor/apex/apexcharts.min.js') }}"></script>

    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>

    <script>
        var options = {
            chart: {
                height: 400,
                type: "bar",
                toolbar: {
                    show: false,
                },
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                curve: "straight",
                width: 1,
            },
            series: @json($grafikData['series']),
            grid: {
                borderColor: "#d8dee6",
                strokeDashArray: 5,
                xaxis: {
                    lines: {
                        show: true,
                    },
                },
                yaxis: {
                    lines: {
                        show: false,
                    },
                },
                padding: {
                    top: 0,
                    right: 0,
                    bottom: 10,
                    left: 0,
                },
            },
            xaxis: {
                categories: @json($grafikData['categories']),
            },
            yaxis: {
                labels: {
                    show: false,
                },
            },
            colors: ["#238781", "#4f9f9a", "#7bb7b3", "#a7cfcd", "#d3e7e6", "#e9f3f2"],
            tooltip: {
                y: {
                    formatter: function(val, {
                        series,
                        seriesIndex,
                        dataPointIndex,
                        w
                    }) {
                        return 'Rp ' + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    },
                },
            },
            markers: {
                size: 0,
                opacity: 0.3,
                colors: ["#238781", "#4f9f9a", "#7bb7b3", "#a7cfcd", "#d3e7e6", "#e9f3f2"],
                strokeColor: "#ffffff",
                strokeWidth: 1,
                hover: {
                    size: 7,
                },
            }
        };

        var chart = new ApexCharts(document.querySelector("#grafikTahunan"), options);

        chart.render();

        // Grafik Kunjungan Harian
        var optionsHarian = {
            chart: {
                height: 350,
                type: 'area',
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            series: @json($grafikKunjungan['harian']['series']),
            xaxis: {
                type: 'category',
                categories: @json($grafikKunjungan['harian']['categories']),
                title: {
                    text: 'Tanggal'
                }
            },
            yaxis: {
                title: {
                    text: 'Jumlah Kunjungan'
                },
                min: 0,
                labels: {
                    formatter: function(val) {
                        return parseInt(val);
                    }
                }
            },
            colors: ["#238781", "#4f9f9a", "#a7cfcd"],
            tooltip: {
                x: {
                    format: 'dd'
                },
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right'
            }
        };
        var chartHarian = new ApexCharts(document.querySelector("#grafikKunjunganHarian"), optionsHarian);
        chartHarian.render();

        // Grafik Kunjungan Bulanan
        var optionsBulanan = {
            chart: {
                height: 350,
                type: 'bar',
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            series: @json($grafikKunjungan['bulanan']['series']),
            xaxis: {
                categories: @json($grafikKunjungan['bulanan']['categories']),
            },
            yaxis: {
                title: {
                    text: 'Jumlah Kunjungan'
                },
                labels: {
                    formatter: function(val) {
                        return parseInt(val);
                    }
                }
            },
            fill: {
                opacity: 1
            },
            colors: ["#238781", "#4f9f9a", "#a7cfcd"],
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " kunjungan"
                    }
                }
            }
        };
        var chartBulanan = new ApexCharts(document.querySelector("#grafikKunjunganBulanan"), optionsBulanan);
        chartBulanan.render();


        // Central function to update operational status display
        function updateOperationalStatusDisplay(ops, withAnimation = true) {
            // Update IGD
            if (ops.igd) {
                const igdPercent = (ops.igd.occupied / ops.igd.total) * 100;
                $('#igd-progress').css('width', igdPercent + '%');
                $('#igd-count').text(ops.igd.occupied + '/' + ops.igd.total);
                if (withAnimation) {
                    $('#igd-count').addClass('updated-flash');
                    setTimeout(() => $('#igd-count').removeClass('updated-flash'), 500);
                }
            }

            // Update Rawat Inap
            if (ops.inpatient) {
                const inpatientPercent = (ops.inpatient.occupied / ops.inpatient.total) * 100;
                $('#inpatient-progress').css('width', inpatientPercent + '%');
                $('#inpatient-count').text(ops.inpatient.occupied + '/' + ops.inpatient.total);
                if (withAnimation) {
                    $('#inpatient-count').addClass('updated-flash');
                    setTimeout(() => $('#inpatient-count').removeClass('updated-flash'), 500);
                }
            }

            // Update Rawat Jalan
            if (ops.outpatient) {
                const outpatientPercent = Math.min((ops.outpatient.today / ops.outpatient.capacity) * 100, 100);
                $('#outpatient-progress').css('width', outpatientPercent + '%');
                $('#outpatient-count').text(ops.outpatient.today + ' kunjungan');
                if (withAnimation) {
                    $('#outpatient-count').addClass('updated-flash');
                    setTimeout(() => $('#outpatient-count').removeClass('updated-flash'), 500);
                }
            }

            // Update Staff Info
            if (ops.staff) {
                $('#doctors-count').text(ops.staff.doctors_on_duty);
                $('#nurses-count').text(ops.staff.nurses_on_duty);
                if (withAnimation) {
                    $('#doctors-count').addClass('updated-flash');
                    $('#nurses-count').addClass('updated-flash');
                    setTimeout(() => {
                        $('#doctors-count').removeClass('updated-flash');
                        $('#nurses-count').removeClass('updated-flash');
                    }, 500);
                }
            }

            // Update Pharmacy & Lab
            if (ops.pharmacy) {
                $('#pharmacy-count').text(ops.pharmacy.active);
                if (withAnimation) {
                    $('#pharmacy-count').addClass('updated-flash');
                    setTimeout(() => $('#pharmacy-count').removeClass('updated-flash'), 500);
                }
            }
            if (ops.lab) {
                $('#lab-queue-count').text(ops.lab.queue);
                if (withAnimation) {
                    $('#lab-queue-count').addClass('updated-flash');
                    setTimeout(() => $('#lab-queue-count').removeClass('updated-flash'), 500);
                }
            }
        }

        // Auto-refresh real-time data function
        function refreshRealTimeData() {
            return $.ajax({
                url: '{{ route('owner.realtime-data') }}',
                method: 'GET',
                timeout: 15000, // 15 second timeout
                success: function(data) {
                    // Update operational status
                    if (data.operational_status) {
                        updateOperationalStatusDisplay(data.operational_status, true);
                    }

                    // Update timestamp indicator
                    $('#refresh-countdown').remove();
                    if (!$('#last-update').length) {
                        $('h5:contains("Status Operasional Real-time")').append(
                            ' <small id="last-update" class="text-success">(Update: ' + data.timestamp +
                            ')</small>');
                    } else {
                        $('#last-update').removeClass('text-danger').addClass('text-success').text('(Update: ' +
                            data.timestamp + ')');
                    }

                    console.log('Real-time data refreshed successfully at ' + data.timestamp);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to refresh real-time data:', error);

                    // Show error indicator
                    $('#refresh-countdown').remove();
                    if (!$('#last-update').length) {
                        $('h5:contains("Status Operasional Real-time")').append(
                            ' <small id="last-update" class="text-danger">(Error loading data)</small>');
                    } else {
                        $('#last-update').removeClass('text-success').addClass('text-danger').text(
                            '(Error loading data)');
                    }
                }
            });
        }

        // Auto-refresh setup with countdown indicator
        let autoRefreshInterval;
        let countdownInterval;
        let refreshCountdown = 60; // 60 seconds

        function startAutoRefresh() {
            // Clear any existing intervals
            if (autoRefreshInterval) clearInterval(autoRefreshInterval);
            if (countdownInterval) clearInterval(countdownInterval);

            // Reset countdown
            refreshCountdown = 60;

            // Start countdown
            countdownInterval = setInterval(function() {
                refreshCountdown--;

                // Update countdown display
                if ($('#refresh-countdown').length) {
                    $('#refresh-countdown').text(`(Auto-refresh dalam ${refreshCountdown}s)`);
                } else if (!$('#last-update').length && !$('#refresh-error').length) {
                    $('h5:contains("Status Operasional Real-time")').append(
                        ' <small id="refresh-countdown" class="text-info">(Auto-refresh dalam ' +
                        refreshCountdown + 's)</small>');
                }

                // Auto refresh when countdown reaches 0
                if (refreshCountdown <= 0) {
                    $('#refresh-countdown').remove();
                    refreshRealTimeData();
                    startAutoRefresh(); // Restart the cycle
                }
            }, 1000);
        }

        // Start auto-refresh after 5 seconds (give page time to load)
        setTimeout(startAutoRefresh, 5000);

        // Pause auto-refresh when user manually refreshes
        $(document).on('click', '#refresh-dashboard', function() {
            if (countdownInterval) {
                clearInterval(countdownInterval);
                $('#refresh-countdown').remove();

                // Restart auto-refresh after manual refresh completes
                setTimeout(startAutoRefresh, 3000);
            }
        });

        // Manual refresh button with improved feedback
        $(document).on('click', '#refresh-dashboard', function(e) {
            e.preventDefault();

            const $btn = $(this);
            const $icon = $btn.find('i');

            // Show loading state
            $btn.prop('disabled', true);
            $icon.removeClass('ri-refresh-line').addClass('ri-loader-4-line');
            $btn.find('.btn-text').text(' Memuat...');

            // Clear any existing indicators
            $('#refresh-error, #refresh-countdown').remove();

            // Use the central refresh function
            refreshRealTimeData()
                .done(function(data) {
                    // Show success feedback
                    $btn.removeClass('btn-outline-primary').addClass('btn-success');
                    $icon.removeClass('ri-loader-4-line').addClass('ri-check-line');
                    $btn.find('.btn-text').text(' Berhasil');

                    setTimeout(() => {
                        $btn.removeClass('btn-success').addClass('btn-outline-primary');
                        $icon.removeClass('ri-check-line').addClass('ri-refresh-line');
                        $btn.find('.btn-text').text(' Refresh');
                        $btn.prop('disabled', false);
                    }, 2000);

                    console.log('Manual refresh completed successfully at ' + data.timestamp);
                })
                .fail(function(xhr, status, error) {
                    console.error('Manual refresh failed:', error);

                    // Show error state
                    $btn.removeClass('btn-outline-primary').addClass('btn-danger');
                    $icon.removeClass('ri-loader-4-line').addClass('ri-error-warning-line');
                    $btn.find('.btn-text').text(' Error');

                    setTimeout(() => {
                        $btn.removeClass('btn-danger').addClass('btn-outline-primary');
                        $icon.removeClass('ri-error-warning-line').addClass('ri-refresh-line');
                        $btn.find('.btn-text').text(' Refresh');
                        $btn.prop('disabled', false);
                    }, 3000);
                });
        });

        // Function untuk menampilkan modal bed terpakai
        function showOccupiedBedsModal() {
            $('#occupiedBedsModal').modal('show');
            loadOccupiedBedsData();
        }

        // Function untuk memuat data pasien bed terpakai
        function loadOccupiedBedsData() {
            $.ajax({
                url: '{{ route('ruangan.occupied-patients') }}',
                method: 'GET',
                beforeSend: function() {
                    $('#occupiedBedsContent').html(`
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Memuat data pasien...</p>
                        </div>
                    `);
                },
                success: function(response) {
                    if (response.success) {
                        displayOccupiedBedsData(response.data);
                    } else {
                        $('#occupiedBedsContent').html(`
                            <div class="alert alert-warning text-center">
                                <i class="ri-information-line"></i>
                                ${response.message || 'Gagal memuat data pasien'}
                            </div>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    $('#occupiedBedsContent').html(`
                        <div class="alert alert-danger text-center">
                            <i class="ri-error-warning-line"></i>
                            Terjadi kesalahan saat memuat data: ${error}
                        </div>
                    `);
                }
            });
        }

        // Function untuk menampilkan data pasien dalam tabel
        function displayOccupiedBedsData(data) {
            let html = `
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No. Kamar</th>
                                <th>Kategori</th>
                                <th>Kelas</th>
                                <th>Nama Pasien</th>
                                <th>No. RM</th>
                                <th>Dokter</th>
                                <th>Tanggal Masuk</th>
                                <th>Lama Rawat</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            if (data.length === 0) {
                html += `
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="ri-information-line fs-3 text-muted"></i>
                            <p class="text-muted mb-0">Tidak ada pasien rawat inap saat ini</p>
                        </td>
                    </tr>
                `;
            } else {
                data.forEach(function(patient, index) {
                    const statusBadge = getStatusBadge(patient.status);
                    const admissionDate = new Date(patient.admission_date).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    });
                    const daysInHospital = Math.ceil((new Date() - new Date(patient.admission_date)) / (1000 * 60 *
                        60 * 24));

                    html += `
                        <tr>
                            <td><strong class="text-primary">${patient.room_number}</strong></td>
                            <td>${patient.category_name}</td>
                            <td><span class="badge bg-info">${patient.class}</span></td>
                            <td><strong>${patient.patient_name}</strong><br><small class="text-muted">${patient.gender}, ${patient.age} tahun</small></td>
                            <td><code>${patient.medical_record}</code></td>
                            <td>${patient.doctor_name}</td>
                            <td>${admissionDate}</td>
                            <td><span class="badge bg-secondary">${daysInHospital} hari</span></td>
                            <td>${statusBadge}</td>
                        </tr>
                    `;
                });
            }

            html += `
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="ri-information-line"></i>
                        Data terakhir diperbarui: ${new Date().toLocaleString('id-ID')}
                    </small>
                </div>
            `;

            $('#occupiedBedsContent').html(html);
        }

        // Function untuk mendapatkan badge status
        function getStatusBadge(status) {
            const statusConfig = {
                'active': {
                    class: 'success',
                    text: 'Aktif'
                },
                'critical': {
                    class: 'danger',
                    text: 'Kritis'
                },
                'stable': {
                    class: 'success',
                    text: 'Stabil'
                },
                'observation': {
                    class: 'warning',
                    text: 'Observasi'
                },
                'recovery': {
                    class: 'info',
                    text: 'Pemulihan'
                }
            };

            const config = statusConfig[status] || {
                class: 'secondary',
                text: 'Unknown'
            };
            return `<span class="badge bg-${config.class}">${config.text}</span>`;
        }

        // Function untuk refresh data pasien
        function refreshOccupiedBeds() {
            loadOccupiedBedsData();
        }

        // Function untuk testing bed logic
        function testBedLogic() {
            $.ajax({
                url: '{{ route('ruangan.test-bed-logic') }}',
                method: 'GET',
                beforeSend: function() {
                    console.log('Testing bed logic...');
                },
                success: function(response) {
                    if (response.success) {
                        console.log('=== BED LOGIC TEST RESULTS ==');
                        console.log('Timestamp:', response.timestamp);
                        console.log('Total Admissions:', response.debug_data.total_admissions);
                        console.log('Active Admissions:', response.debug_data.active_admissions);
                        console.log('Discharged Admissions:', response.debug_data.discharged_admissions);
                        console.log('Bed Summary:', response.debug_data.bed_summary);
                        console.log('Active Patients:', response.debug_data.active_patients_detail);
                        console.log('Discharged Patients:', response.debug_data.discharged_patients_detail);

                        // Show alert with summary
                        alert(`BED LOGIC TEST RESULTS:\n\n` +
                            `Total Admissions: ${response.debug_data.total_admissions}\n` +
                            `Active Admissions: ${response.debug_data.active_admissions}\n` +
                            `Discharged Admissions: ${response.debug_data.discharged_admissions}\n\n` +
                            `CURRENT BED STATUS:\n` +
                            `Total Beds: ${response.debug_data.bed_summary.total_beds}\n` +
                            `Occupied Beds: ${response.debug_data.bed_summary.occupied_beds}\n` +
                            `Available Beds: ${response.debug_data.bed_summary.available_beds}\n` +
                            `Occupancy Rate: ${response.debug_data.bed_summary.occupancy_rate}%\n\n` +
                            `✅ Check browser console for detailed data!`);
                    } else {
                        alert('Test failed: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Test bed logic error:', error);
                    alert('Error running test: ' + error);
                }
            });
        }

        // Function untuk cleanup admission data
        function cleanupAdmissionData() {
            if (!confirm('This will cleanup invalid admission data automatically. Continue?')) {
                return;
            }

            $.ajax({
                url: '{{ route('ruangan.cleanup-admission-data') }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    console.log('Cleaning up admission data...');
                },
                success: function(response) {
                    if (response.success) {
                        console.log('=== DATA CLEANUP RESULTS ===');
                        console.log('Timestamp:', response.timestamp);
                        console.log('Results:', response.results);

                        let message = `DATA CLEANUP COMPLETED:\n\n`;
                        message += `Invalid Admissions Found: ${response.results.invalid_admissions_found}\n`;
                        message += `Orphaned Admissions: ${response.results.orphaned_admissions}\n`;
                        message += `Wrong Type Admissions: ${response.results.wrong_type_admissions}\n\n`;
                        message += `AFTER CLEANUP:\n`;
                        message += `Total Beds: ${response.results.current_bed_summary.total_beds}\n`;
                        message += `Occupied Beds: ${response.results.current_bed_summary.occupied_beds}\n`;
                        message += `Available Beds: ${response.results.current_bed_summary.available_beds}\n`;
                        message +=
                            `Occupancy Rate: ${response.results.current_bed_summary.occupancy_rate}%\n\n`;
                        message += `✅ Check browser console for detailed data!`;

                        alert(message);

                        // Refresh the modal data
                        loadOccupiedBedsData();

                        // Refresh the page to show updated bed counts
                        setTimeout(() => {
                            location.reload();
                        }, 2000);

                    } else {
                        alert('Cleanup failed: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Cleanup error:', error);
                    alert('Error running cleanup: ' + error);
                }
            });
        }

        // Function untuk testing KPI accuracy
        function testKPIAccuracy() {
            $.ajax({
                url: '{{ route('ruangan.test-kpi-accuracy') }}',
                method: 'GET',
                beforeSend: function() {
                    console.log('Testing KPI accuracy...');
                },
                success: function(response) {
                    if (response.success) {
                        console.log('=== KPI ACCURACY TEST RESULTS ===');
                        console.log('Timestamp:', response.timestamp);
                        console.log('Current KPI Data:', response.current_kpi_data);
                        console.log('Test Results:', response.test_results);

                        let message = `KPI ACCURACY TEST RESULTS:\n\n`;

                        // Bed Occupancy
                        const bedOcc = response.test_results.bed_occupancy;
                        message += `BED OCCUPANCY:\n`;
                        message += `Rate: ${bedOcc.from_kpi_method}% (${bedOcc.status})\n`;
                        message += `Occupied: ${bedOcc.occupied_beds}/${bedOcc.total_beds} beds\n\n`;

                        // Average LOS
                        const avgLos = response.test_results.average_los;
                        message += `AVERAGE LENGTH OF STAY:\n`;
                        message += `${avgLos.calculated_los} days\n`;
                        message += `Based on ${avgLos.discharged_patients_this_month} discharged patients\n\n`;

                        // Revenue per Patient
                        const revPerPatient = response.test_results.revenue_per_patient;
                        message += `REVENUE PER PATIENT:\n`;
                        message += `${revPerPatient.formatted_rev_per_patient}\n`;
                        message += `From ${revPerPatient.total_patients} patients\n\n`;

                        // Patient Satisfaction
                        const satisfaction = response.test_results.patient_satisfaction;
                        message += `PATIENT SATISFACTION:\n`;
                        message += `${satisfaction.calculated_satisfaction}%\n`;
                        message +=
                            `Based on ${satisfaction.completed_encounters_this_month} completed encounters\n\n`;

                        // Operational Status Verification
                        const operational = response.test_results.operational_status;
                        message += `OPERATIONAL STATUS VERIFICATION:\n`;
                        message +=
                            `Inpatient matches bed summary: ${operational.verification.inpatient_matches_bed_summary ? 'YES' : 'NO'}\n\n`;

                        message += `✅ Check browser console for detailed data!`;

                        alert(message);

                    } else {
                        alert('KPI test failed: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('KPI test error:', error);
                    alert('Error running KPI test: ' + error);
                }
            });
        }

        // Make functions global
        window.showOccupiedBedsModal = showOccupiedBedsModal;
        window.refreshOccupiedBeds = refreshOccupiedBeds;
        window.testBedLogic = testBedLogic;
        window.cleanupAdmissionData = cleanupAdmissionData;
        window.testKPIAccuracy = testKPIAccuracy;

        // Initialize tooltips for department cards and other elements
        $(document).ready(function() {
            // Initialize Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Add smooth scroll behavior for better UX
            $('html').css('scroll-behavior', 'smooth');

            // Initialize department card animations
            $('.department-card').on('mouseenter', function() {
                $(this).find('.progress-bar').addClass('animate__animated animate__pulse');
            }).on('mouseleave', function() {
                $(this).find('.progress-bar').removeClass('animate__animated animate__pulse');
            });

            // Debug Remix Icon font loading
            console.log('🔍 Checking Remix Icon font loading...');

            // Check if remixicon font is loaded
            if (document.fonts) {
                document.fonts.ready.then(() => {
                    console.log('📝 All fonts loaded. Checking icons...');
                    checkIconsLoading();
                });
            } else {
                // Fallback for older browsers
                setTimeout(checkIconsLoading, 1000);
            }

            function checkIconsLoading() {
                // Debug all remix icons on page
                const allRemixIcons = $('[class*="ri-"]');
                console.log(`🎯 Found ${allRemixIcons.length} Remix Icon elements`);

                // Specifically check IGD icon
                const igdIcon = $('.ri-first-aid-kit-line');
                if (igdIcon.length > 0) {
                    console.log('🏥 IGD Icon element found:', igdIcon.length);

                    // Check computed style
                    try {
                        const element = igdIcon[0];
                        const computedStyle = window.getComputedStyle(element, ':before');
                        const content = computedStyle.getPropertyValue('content');
                        const fontFamily = computedStyle.getPropertyValue('font-family');

                        console.log('🔍 IGD Icon Debug Info:');
                        console.log('   - Content:', content);
                        console.log('   - Font Family:', fontFamily);
                        console.log('   - Element classes:', element.className);

                        if (content && content !== 'none' && content !== '""') {
                            console.log('✅ IGD Icon loaded successfully!');
                            igdIcon.addClass('icon-loaded');
                        } else {
                            console.warn('⚠️ IGD Icon not loading properly. Using emoji fallback.');
                            // Replace with emoji as fallback
                            igdIcon.removeClass('ri-first-aid-kit-line')
                                .addClass('fallback-icon')
                                .html('🏥');
                        }
                    } catch (error) {
                        console.error('❌ Error checking IGD icon:', error);
                        igdIcon.removeClass('ri-first-aid-kit-line')
                            .addClass('fallback-icon')
                            .html('🏥');
                    }
                } else {
                    console.error('❌ IGD Icon element not found in DOM!');
                }
            }

            console.log('✨ Owner Dashboard initialized successfully!');
        });
    </script>
@endpush
