@extends('layouts.app')
@section('title')
    Dashboard Keuangan
@endsection
@push('style')
    <!-- *************
                          ************ Vendor Css Files *************
                         ************ -->

    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">

    <!-- Date Range CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/daterange/daterange.css') }}">

    <!-- Data Tables -->
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bs5.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bs5-custom.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/buttons/dataTables.bs5-custom.css') }}">
@endpush
@section('content')
    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-xxl-12 col-sm-12">

            <!-- Row starts -->
            <div class="row gx-3">
                <div class="col-sm-3 col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="p-2 border border-primary rounded-circle me-3">
                                    <div class="icon-box md bg-primary-lighten rounded-5">
                                        <i class="ri-cash-line fs-4 text-primary"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <h1 class="lh-1">{{ formatPrice($totalPendapatanBulanIni) }}</h1>
                                    <p class="m-0">Pendapatan</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-1">
                                <a class="text-primary" href="javascript:void(0);">
                                    <span>View All</span>
                                    <i class="ri-arrow-right-line text-primary ms-1"></i>
                                </a>
                                <div class="text-end">
                                    <p class="mb-0 text-primary">+40%</p>
                                    <span class="badge bg-primary-light text-primary small">this
                                        month</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="p-2 border border-primary rounded-circle me-3">
                                    <div class="icon-box md bg-primary-lighten rounded-5">
                                        <i class="ri-cash-line fs-4 text-primary"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <h1 class="lh-1">{{ formatPrice($pengeluaranOperasionalBulanIni) }}</h1>
                                    <p class="m-0">Pengeluaran Operasional</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-1">
                                <a class="text-primary" href="javascript:void(0);">
                                    <span>View All</span>
                                    <i class="ri-arrow-right-line ms-1"></i>
                                </a>
                                <div class="text-end">
                                    <p class="mb-0 text-primary">+30%</p>
                                    <span class="badge bg-primary-light text-primary small">this
                                        month</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="p-2 border border-primary rounded-circle me-3">
                                    <div class="icon-box md bg-primary-lighten rounded-5">
                                        <i class="ri-cash-line fs-4 text-primary"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <h1 class="lh-1">{{ formatPrice($gajiInsentifBulanIni) }}</h1>
                                    <p class="m-0">Gaji & Isentif</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-1">
                                <a class="text-primary" href="javascript:void(0);">
                                    <span>View All</span>
                                    <i class="ri-arrow-right-line ms-1"></i>
                                </a>
                                <div class="text-end">
                                    <p class="mb-0 text-primary">+30%</p>
                                    <span class="badge bg-primary-light text-primary small">this
                                        month</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="p-2 border border-primary rounded-circle me-3">
                                    <div class="icon-box md bg-primary-lighten rounded-5">
                                        <i class="ri-money-dollar-circle-line fs-4 text-primary"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <h1 class="lh-1">{{ formatPrice($labaRugiBulanIni) }}</h1>
                                    <p class="m-0">Laba Rugi</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-1">
                                <a class="text-primary" href="{{ route('operasional.index') }}">
                                    <span>View All</span>
                                    <i class="ri-arrow-right-line ms-1"></i>
                                </a>
                                <div class="text-end">
                                    <p class="mb-0 text-primary">+20%</p>
                                    <span class="badge bg-primary-light text-primary small">this
                                        month</span>
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
                <div class="card-header pb-0">
                    <h5 class="card-title">Grafik Tahun {{ date('Y') }}</h5>
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
    <!-- Row ends -->
@endsection
@push('scripts')
    <!-- *************
                           ************ Vendor Js Files *************
                          ************* -->

    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>

    <!-- Date Range JS -->
    <script src="{{ asset('vendor/daterange/daterange.js') }}"></script>
    <script src="{{ asset('vendor/daterange/custom-daterange.js') }}"></script>

    <!-- Apex Charts -->
    <script src="{{ asset('vendor/apex/apexcharts.min.js') }}"></script>

    <!-- Data Tables -->
    <script src="{{ asset('vendor/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/custom/custom-datatables.js') }}"></script>

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
            },
        };

        var chart = new ApexCharts(document.querySelector("#grafikTahunan"), options);

        chart.render();
    </script>
@endpush
