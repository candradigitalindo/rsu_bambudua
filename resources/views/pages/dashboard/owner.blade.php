@extends('layouts.app')
@section('title')
    Dashboard Owner
@endsection
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
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
    </script>
@endpush
