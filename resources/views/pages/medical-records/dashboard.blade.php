@extends('layouts.app')
@section('title')
    Dashboard Rekam Medis
@endsection
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <style>
        .stat-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .chart-container {
            min-height: 350px;
        }
    </style>
@endpush
@section('content')
    <!-- Header Section -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Dashboard Rekam Medis</h4>
                    <p class="text-muted mb-0">Ringkasan data rekam medis dan statistik kunjungan pasien</p>
                </div>
                <div class="text-muted">
                    <i class="ri-calendar-line"></i> {{ now()->isoFormat('dddd, D MMMM Y') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row gx-3">
        <div class="col-xxl-12 col-sm-12">
            <div class="row gx-3">
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card mb-3 stat-card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted mb-1 small">Total Pasien Terdaftar</p>
                                    <h2 class="mb-0 fw-bold">{{ number_format($totalPasien) }}</h2>
                                </div>
                                <div class="p-3 bg-primary bg-opacity-10 rounded-circle">
                                    <i class="ri-folder-user-line fs-2 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card mb-3 stat-card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted mb-1 small">Total Kunjungan</p>
                                    <h2 class="mb-0 fw-bold">{{ number_format($totalEncounter) }}</h2>
                                </div>
                                <div class="p-3 bg-info bg-opacity-10 rounded-circle">
                                    <i class="ri-stethoscope-line fs-2 text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card mb-3 stat-card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted mb-1 small">Kunjungan Bulan Ini</p>
                                    <h2 class="mb-0 fw-bold">{{ number_format($bulanIniEncounter) }}</h2>
                                </div>
                                <div class="p-3 bg-success bg-opacity-10 rounded-circle">
                                    <i class="ri-calendar-check-line fs-2 text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card mb-3 stat-card border-0 shadow-sm">
                        <div class="card-body">
                            <div>
                                <p class="text-muted mb-2 small">Distribusi Tipe Kunjungan</p>
                                <div class="d-flex gap-3 flex-wrap">
                                    <div>
                                        <small class="text-muted d-block">Rawat Jalan</small>
                                        <strong class="text-primary">{{ number_format($rawatJalan) }}</strong>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">IGD</small>
                                        <strong class="text-warning">{{ number_format($igd) }}</strong>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Rawat Inap</small>
                                        <strong class="text-danger">{{ number_format($rawatInap) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row gx-3">
                <div class="col-lg-8 col-12">
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-0">Grafik Kunjungan Pasien</h5>
                                    <small class="text-muted">Tahun {{ date('Y') }}</small>
                                </div>
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    <i class="ri-bar-chart-line"></i> Tahunan
                                </span>
                            </div>
                        </div>
                        <div class="card-body chart-container">
                            <div id="grafikKunjunganTahunan"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="card-title mb-0">Top 5 Diagnosis</h5>
                            <small class="text-muted">Diagnosis paling sering</small>
                        </div>
                        <div class="card-body">
                            <div id="chartTopDiagnosis"></div>
                        </div>
                    </div>
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="card-title mb-0">
                                <i class="ri-shield-check-line text-success"></i> Kepatuhan & Standar RS
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex align-items-start">
                                    <i class="ri-check-line text-success me-2 mt-1"></i>
                                    <small class="text-muted">Privasi & kerahasiaan pasien dijaga sesuai regulasi</small>
                                </div>
                                <div class="d-flex align-items-start">
                                    <i class="ri-check-line text-success me-2 mt-1"></i>
                                    <small class="text-muted">Audit akses rekam medis dapat ditelusuri</small>
                                </div>
                                <div class="d-flex align-items-start">
                                    <i class="ri-check-line text-success me-2 mt-1"></i>
                                    <small class="text-muted">Format sesuai standar (ICD-10, status pulang)</small>
                                </div>
                                <div class="d-flex align-items-start">
                                    <i class="ri-check-line text-success me-2 mt-1"></i>
                                    <small class="text-muted">Akses berbasis role & permission</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <script src="{{ asset('vendor/apex/apexcharts.min.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        var options = {
            chart: {
                height: 350,
                type: "bar",
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: false,
                        zoom: false,
                        zoomin: false,
                        zoomout: false,
                        pan: false,
                        reset: false
                    }
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 8,
                    columnWidth: '60%',
                    distributed: false
                }
            },
            dataLabels: {
                enabled: true,
                style: {
                    fontSize: '11px',
                    colors: ['#fff']
                }
            },
            stroke: {
                curve: "smooth",
                width: 2
            },
            series: @json($grafikData['series']),
            grid: {
                borderColor: "#e7e7e7",
                strokeDashArray: 4,
                xaxis: {
                    lines: {
                        show: true
                    }
                },
                yaxis: {
                    lines: {
                        show: true
                    }
                },
                padding: {
                    top: 0,
                    right: 20,
                    bottom: 0,
                    left: 10
                }
            },
            xaxis: {
                categories: @json($grafikData['categories']),
                labels: {
                    style: {
                        fontSize: '12px',
                        colors: '#666'
                    }
                }
            },
            yaxis: {
                labels: {
                    show: true,
                    style: {
                        fontSize: '12px',
                        colors: '#666'
                    }
                }
            },
            colors: ["#238781", "#4f9f9a", "#7bb7b3"],
            legend: {
                show: true,
                position: 'top',
                horizontalAlign: 'right'
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " kunjungan"
                    }
                }
            }
        };
        new ApexCharts(document.querySelector("#grafikKunjunganTahunan"), options).render();

        // Top 5 Diagnosis donut chart
        const topDiagLabels = @json($topDiag['labels'] ?? []);
        const topDiagSeries = @json($topDiag['data'] ?? []);
        new ApexCharts(document.querySelector('#chartTopDiagnosis'), {
            chart: {
                type: 'donut',
                height: 280,
                toolbar: {
                    show: false
                }
            },
            labels: topDiagLabels,
            series: topDiagSeries,
            colors: ['#238781', '#4f9f9a', '#7bb7b3', '#a7cfcd', '#d3e7e6'],
            dataLabels: {
                enabled: true,
                formatter: function(val, opts) {
                    return val.toFixed(1) + '%';
                },
                style: {
                    fontSize: '12px',
                    fontWeight: 'bold',
                    colors: ['#fff']
                },
                dropShadow: {
                    enabled: false
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            name: {
                                show: true,
                                fontSize: '14px',
                                color: '#666'
                            },
                            value: {
                                show: true,
                                fontSize: '22px',
                                fontWeight: 'bold',
                                color: '#238781',
                                formatter: function(val) {
                                    return Math.round(val);
                                }
                            },
                            total: {
                                show: true,
                                label: 'Total Diagnosis',
                                fontSize: '12px',
                                color: '#999',
                                formatter: function(w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                }
                            }
                        }
                    }
                }
            },
            legend: {
                show: true,
                position: 'bottom',
                fontSize: '11px',
                offsetY: 5,
                formatter: function(seriesName, opts) {
                    var v = opts.w.globals.series[opts.seriesIndex];
                    var sum = opts.w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                    var pct = sum ? (v / sum * 100).toFixed(1) : 0;
                    return '<span style="color:#666">' + seriesName + '</span> <strong>' + pct + '%</strong>';
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " kasus"
                    }
                }
            }
        }).render();
    </script>
@endpush
