@extends('layouts.app')
@section('title', 'Dashboard Dokter')
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">

    <!-- Uploader CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.min.css') }}">
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
        <div class="col-sm-12">
            <div class="card mb-3 border">
                <div class="card-body">

                    <!-- Row starts -->
                    <div class="row gx-3">
                        <div class="col-xl-6 col-sm-6">
                            <div class="d-flex align-items-start gap-3 flex-wrap">
                                <img src="{{ $user->profile->foto ? route('home.profile.filename', $user->profile->foto) : asset('images/no Photo.png') }}"
                                    class="mw-240 rounded-2" alt="Medical Dashboard">

                                <div class="">
                                    <span class="badge bg-primary-subtle text-primary"><i
                                            class="ri-circle-fill me-1"></i>Available</span>
                                    <h6 class="fw-normal mt-2">Selamat Datang,</h6>
                                    <h5 class="fw-normal">{{ $user->name }}</h5>
                                    <h6 class="fw-normal">Poliklinik :
                                        {{ implode(', ', $user->clinics->pluck('nama')->toArray()) }}</h6>

                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-sm-6">

                            <!-- Row starts -->
                            <div class="row g-3">
                                <div class="col-sm-4">
                                    <div class="card border rounded-5">
                                        <div class="card-body">

                                            <!-- Card details start -->
                                            <div class="text-center">
                                                <div class="icon-box md border border-primary rounded-5 mb-2 m-auto">
                                                    <i class="ri-stethoscope-line fs-5 text-primary"></i>
                                                </div>
                                                <h3 class="text-primary">{{ $thisMonth_rawatJalan }}</h3>
                                                <h6>Rawat Jalan</h6>
                                                @php
                                                    $isUp_rawatJalan = $percent_rawatJalan >= 0;
                                                @endphp
                                                <small class="{{ $isUp_rawatJalan ? 'text-primary' : 'text-danger' }}">
                                                    @if ($percent_rawatJalan >= 0)
                                                        <i class="ri-arrow-up-s-line text-primary"></i>
                                                    @else
                                                        <i class="ri-arrow-down-s-line text-danger"></i>
                                                    @endif
                                                    {{ number_format(abs($percent_rawatJalan), 0) }}%
                                                    {{ $isUp_rawatJalan ? 'dari minggu lalu' : 'dari minggu lalu' }}
                                                </small>
                                            </div>
                                            <!-- Card details end -->

                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="card border rounded-5">
                                        <div class="card-body">

                                            <!-- Card details start -->
                                            <div class="text-center">
                                                <div class="icon-box md border border-primary rounded-5 mb-2 m-auto">
                                                    <i class="ri-hotel-bed-fill fs-5 text-primary"></i>
                                                </div>
                                                <h3 class="text-primary">{{ $thisMonth_inpatient }}</h3>
                                                <h6>Rawat Inap</h6>
                                                @php
                                                    $isUp_inpatient = $percent_inpatient >= 0;
                                                @endphp
                                                <small class="{{ $isUp_inpatient ? 'text-primary' : 'text-danger' }}">
                                                    @if ($percent_inpatient >= 0)
                                                        <i class="ri-arrow-up-s-line text-primary"></i>
                                                    @else
                                                        <i class="ri-arrow-down-s-line text-danger"></i>
                                                    @endif
                                                    {{ number_format(abs($percent_inpatient), 0) }}% dari minggu lalu
                                                </small>
                                            </div>
                                            <!-- Card details end -->

                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="card border rounded-5">
                                        <div class="card-body">

                                            <!-- Card details start -->
                                            <div class="text-center">
                                                <div class="icon-box md border border-primary rounded-5 mb-2 m-auto">
                                                    <i class="ri-dossier-fill fs-5 text-primary"></i>
                                                </div>
                                                <h3 class="text-primary">{{ $thisWeek_rawatDarurat }}</h3>
                                                <h6>IGD</h6>
                                                @php
                                                    $isUp_rawatDarurat = $percent_rawatDarurat >= 0;
                                                @endphp
                                                <small class="{{ $isUp_rawatDarurat ? 'text-primary' : 'text-danger' }}">
                                                    @if ($percent_rawatDarurat >= 0)
                                                        <i class="ri-arrow-up-s-line text-primary"></i>
                                                    @else
                                                        <i class="ri-arrow-down-s-line text-danger"></i>
                                                    @endif
                                                    {{ number_format(abs($percent_rawatDarurat), 0) }}%
                                                    dari minggu lalu
                                                </small>
                                            </div>
                                            <!-- Card details end -->

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Row ends -->

                        </div>
                    </div>
                    <!-- Row ends -->

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
                    <h5 class="card-title">Grafik Kunjungan Harian Anda (Bulan {{ now()->translatedFormat('F') }})</h5>
                </div>
                <div class="card-body pt-0">
                    <div id="grafikKunjunganHarian"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Grafik Kunjungan Bulanan Anda (Tahun {{ date('Y') }})</h5>
                </div>
                <div class="card-body pt-0">
                    <div id="grafikKunjunganBulanan"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Row ends -->
    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-sm-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Kunjungan Pasien Tahun {{ date('Y') }}</h5>
                </div>
                <div class="card-body">

                    @php
                        $persenEncounter = $grafik['persen_total_encounter'] >= 0;
                    @endphp
                    <div
                        class="card-info {{ $persenEncounter ? 'bg-primary-subtle' : 'bg-danger-subtle' }} rounded-1 small">
                        @if ($grafik['persen_total_encounter'] >= 0)
                            <i class="ri-arrow-up-s-line text-primary"></i>
                        @else
                            <i class="ri-arrow-down-s-line text-danger"></i>
                        @endif
                        {{ number_format(abs($grafik['persen_total_encounter']), 0) }}%
                        {{ $persenEncounter ? 'lebih tinggi' : 'lebih rendah' }} dari tahun lalu
                    </div>
                    <div id="pasien"></div>

                </div>
            </div>
        </div>
    </div>
    <!-- Row ends -->
    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-xxl-12 col-sm-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Pendapatan Tahun {{ date('Y') }}</h5>
                </div>
                <div class="card-body">
                    <div class="scroll300 text-center">
                        <div id="grafikPendapatan"></div>
                        <h5 class="mt-2">Total: {{ formatPrice($grafikPendapatan['total_tahun_ini']) }}</h5>
                    </div>

                </div>
            </div>
        </div>

    </div>
    <!-- Row ends -->
    <!-- Row starts -->
    {{-- <div class="row gx-3">
        <div class="col-sm-12">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Data Pasien Bulan Ini</h5>
                    <!-- Table starts -->
                    <div class="table-responsive">
                        <table id="hideSearchExample" class="table m-0 align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient Name</th>
                                    <th>Age</th>
                                    <th>Time</th>
                                    <th>Diagnosis</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>001</td>
                                    <td>
                                        <img src="{{ asset('images/patient.png') }}" class="img-2x rounded-5 me-1"
                                            alt="Doctors Admin Template">
                                        Willian Mathews
                                    </td>
                                    <td>21</td>
                                    <td>
                                        10:30AM
                                    </td>
                                    <td>Heart Attack</td>
                                    <td>
                                        <span class="badge bg-warning-subtle text-warning fs-6">General</span>
                                    </td>
                                    <td>
                                        <div class="d-inline-flex gap-1">
                                            <button type="button" class="btn btn-hover btn-sm rounded-5"
                                                data-bs-toggle="modal" data-bs-target="#delRow">
                                                <span data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-title="Reject">
                                                    <i class="ri-close-fill"></i>
                                                </span>
                                            </button>
                                            <button type="button" class="btn btn-hover btn-sm rounded-5"
                                                data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Approve">
                                                <i class="ri-check-fill"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    <!-- Table ends -->

                    <!-- Modal Delete Row -->
                    <div class="modal fade" id="delRow" tabindex="-1" aria-labelledby="delRowLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="delRowLabel">
                                        Confirm
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to reject the Appointment?
                                </div>
                                <div class="modal-footer">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                                            aria-label="Close">No</button>
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                                            aria-label="Close">Yes</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div> --}}
    <!-- Row ends -->

@endsection
@push('scripts')
    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>

    <!-- Dropzone JS -->
    <script src="{{ asset('vendor/dropzone/dropzone.min.js') }}"></script>

    <!-- Apex Charts -->
    <script src="{{ asset('vendor/apex/apexcharts.min.js') }}"></script>

    <!-- Raty JS -->
    <script src="{{ asset('vendor/rating/raty.js') }}"></script>
    <script src="{{ asset('vendor/rating/raty-custom.js') }}"></script>

    <!-- Data Tables -->
    <script src="{{ asset('vendor/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/custom/custom-datatables.js') }}"></script>

    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="{{ asset('js/validations.js') }}"></script>
    <script>
        $(document).ready(function() {
            var options = {
                chart: {
                    height: 300,
                    type: "line",
                    toolbar: {
                        show: false,
                    },
                },
                dataLabels: {
                    enabled: false,
                },
                stroke: {
                    curve: "smooth",
                    width: 5,
                },
                series: [{
                        name: "Rawat Jalan",
                        data: @json($grafik['rawat_jalan']),
                    },
                    {
                        name: "Rawat Darurat",
                        data: @json($grafik['rawat_darurat']),
                    },
                    {
                        name: "Rawat Inap",
                        data: @json($grafik['rawat_inap']),
                    }
                ],
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
                        bottom: 0,
                        left: 0,
                    },
                },
                xaxis: {
                    categories: [
                        "Jan",
                        "Feb",
                        "Mar",
                        "Apr",
                        "May",
                        "Jun",
                        "Jul",
                        "Aug",
                        "Sep",
                        "Oct",
                        "Nov",
                        "Dec",
                    ],
                },
                yaxis: {
                    labels: {
                        show: false,
                    },
                },
                colors: ["#238781", "#a7cfcd", "#d3e7e6", "#e9f3f2"],
                markers: {
                    size: 0,
                    opacity: 0.3,
                    colors: ["#238781", "#a7cfcd", "#d3e7e6", "#e9f3f2"],
                    strokeColor: "#ffffff",
                    strokeWidth: 1,
                    hover: {
                        size: 7,
                    },
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val;
                        },
                    },
                },
            };

            var chart = new ApexCharts(document.querySelector("#pasien"), options);

            chart.render();
            $("#submit").submit(function() {
                $(".spinner-border").removeClass("d-none");
                $("#btn-update").attr("disabled", true);
                $(".btn-txt").text("Mohon Tunggu ...");
            });

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

            // Grafik Pendapatan
            var optionsPendapatan = {
                chart: {
                    height: 300,
                    type: 'area',
                    toolbar: {
                        show: false,
                    },
                },
                dataLabels: {
                    enabled: false,
                },
                stroke: {
                    curve: 'smooth',
                    width: 2,
                },
                series: @json($grafikPendapatan['series']),
                xaxis: {
                    categories: @json($grafikPendapatan['categories']),
                },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            if (val >= 1000000) {
                                return (val / 1000000).toFixed(1) + ' Jt';
                            } else if (val >= 1000) {
                                return (val / 1000).toFixed(0) + ' Rb';
                            }
                            return val;
                        }
                    }
                },
                colors: ["#238781"],
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return "Rp " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        },
                    },
                },
                grid: {
                    borderColor: '#f1f1f1',
                    strokeDashArray: 5,
                },
            };

            var chartPendapatan = new ApexCharts(
                document.querySelector("#grafikPendapatan"),
                optionsPendapatan
            );
            chartPendapatan.render();
        });
    </script>
@endpush
