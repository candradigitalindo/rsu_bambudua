@extends('layouts.app')
@section('title')
    Dashboard Rekam Medis
@endsection
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
    <div class="row gx-3">
        <div class="col-xxl-12 col-sm-12">
            <div class="row gx-3">
                <div class="col-sm-3 col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="p-2 border border-primary rounded-circle me-3">
                                    <div class="icon-box md bg-primary-lighten rounded-5">
                                        <i class="ri-folder-user-line fs-4 text-primary"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <h2 class="lh-1">{{ $totalPasien }}</h2>
                                    <p class="m-0">Total Pasien</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="p-2 border border-info rounded-circle me-3">
                                    <div class="icon-box md bg-info-lighten rounded-5">
                                        <i class="ri-stethoscope-line fs-4 text-info"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <h2 class="lh-1">{{ $totalEncounter }}</h2>
                                    <p class="m-0">Total Kunjungan</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="p-2 border border-success rounded-circle me-3">
                                    <div class="icon-box md bg-success-lighten rounded-5">
                                        <i class="ri-calendar-check-line fs-4 text-success"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <h2 class="lh-1">{{ $bulanIniEncounter }}</h2>
                                    <p class="m-0">Kunjungan Bulan Ini</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="p-2 border border-warning rounded-circle me-3">
                                    <div class="icon-box md bg-warning-lighten rounded-5">
                                        <i class="ri-hotel-bed-line fs-4 text-warning"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <small class="m-0">RJ: {{ $rawatJalan }} | RI: {{ $rawatInap }} | IGD:
                                        {{ $igd }}</small>
                                    <p class="m-0">Distribusi Tipe Kunjungan</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row gx-3">
                <div class="col-lg-8 col-12">
                    <div class="card mb-3">
                        <div class="card-header pb-0">
                            <h5 class="card-title">Grafik Kunjungan Tahun {{ date('Y') }}</h5>
                        </div>
                        <div class="card-body pt-0">
                            <div id="grafikKunjunganTahunan"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title">Kepatuhan & Standar RS</h5>
                        </div>
                        <div class="card-body small">
                            <ul class="m-0 ps-3">
                                <li>Privasi & Kerahasiaan Pasien dijaga. Data medis hanya untuk keperluan pelayanan.</li>
                                <li>Audit akses rekam medis dapat ditelusuri (log sistem).</li>
                                <li>Istilah dan format sesuai standar RS (ICD-10, tipe kunjungan, status pulang).</li>
                                <li>Akses berbasis role (Admin, RM, Dokter, dsb.).</li>
                            </ul>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-header pb-0">
                            <h5 class="card-title">Top 5 Diagnosis</h5>
                        </div>
                        <div class="card-body pt-0">
                            <div id="chartTopDiagnosis"></div>
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
                height: 400,
                type: "bar",
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: "straight",
                width: 1
            },
            series: @json($grafikData['series']),
            grid: {
                borderColor: "#d8dee6",
                strokeDashArray: 5,
                xaxis: {
                    lines: {
                        show: true
                    }
                },
                yaxis: {
                    lines: {
                        show: false
                    }
                },
                padding: {
                    top: 0,
                    right: 0,
                    bottom: 10,
                    left: 0
                }
            },
            xaxis: {
                categories: @json($grafikData['categories'])
            },
            yaxis: {
                labels: {
                    show: false
                }
            },
            colors: ["#238781", "#4f9f9a", "#7bb7b3", "#a7cfcd", "#d3e7e6", "#e9f3f2"],
        };
        new ApexCharts(document.querySelector("#grafikKunjunganTahunan"), options).render();

        // Top 5 Diagnosis donut (code - name) — gunakan legend untuk persentase agar tidak menumpuk di luar chart
        const topDiagLabels = @json($topDiag['labels'] ?? []);
        const topDiagSeries = @json($topDiag['data'] ?? []);
        new ApexCharts(document.querySelector('#chartTopDiagnosis'), {
            chart: {
                type: 'donut',
                height: 300,
                toolbar: {
                    show: false
                }
            },
            labels: topDiagLabels,
            series: topDiagSeries,
            colors: ['#238781', '#4f9f9a', '#7bb7b3', '#a7cfcd', '#d3e7e6'],
            dataLabels: {
                enabled: false
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            value: {
                                formatter: function(val) {
                                    return Math.round(val);
                                }
                            },
                            total: {
                                show: true,
                                label: 'Total',
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
                formatter: function(seriesName, opts) {
                    var v = opts.w.globals.series[opts.seriesIndex];
                    var sum = opts.w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                    var pct = sum ? (v / sum * 100).toFixed(1) : 0;
                    return seriesName + ' - ' + pct + '%';
                }
            }
        }).render();
    </script>
@endpush
