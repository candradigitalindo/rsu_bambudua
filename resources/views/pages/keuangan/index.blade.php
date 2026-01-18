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

    <style>
        /* Custom styling for date range picker */
        #dateRangeFilter {
            background-color: white !important;
            font-size: 0.9375rem;
            padding: 0.5rem 0.75rem;
        }

        #dateRangeFilter::placeholder {
            color: #a0aec0;
        }

        .input-group-text {
            padding: 0.5rem 0.75rem;
        }

        /* Style the daterangepicker popup */
        .daterangepicker {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
        }

        .daterangepicker .calendar-table th,
        .daterangepicker .calendar-table td {
            border-radius: 0.25rem;
        }

        .daterangepicker td.active,
        .daterangepicker td.active:hover {
            background-color: #238781 !important;
            border-color: #238781 !important;
            color: white !important;
        }

        .daterangepicker td.start-date,
        .daterangepicker td.end-date {
            background-color: #238781 !important;
            border-color: #238781 !important;
            color: white !important;
        }

        .daterangepicker td.start-date.end-date {
            background-color: #238781 !important;
        }

        .daterangepicker td.in-range {
            background-color: rgba(35, 135, 129, 0.3) !important;
            color: #238781 !important;
            font-weight: 600 !important;
        }

        .daterangepicker td.in-range:hover {
            background-color: rgba(35, 135, 129, 0.4) !important;
            color: #1a5652 !important;
        }

        .daterangepicker td.available:hover {
            background-color: rgba(35, 135, 129, 0.3) !important;
            color: #238781 !important;
            font-weight: 600 !important;
        }

        /* Memastikan tanggal selected terlihat jelas */
        .daterangepicker td.active.start-date {
            background-color: #238781 !important;
            color: white !important;
            font-weight: bold !important;
        }

        .daterangepicker td.active.end-date {
            background-color: #238781 !important;
            color: white !important;
            font-weight: bold !important;
        }

        .daterangepicker td.active.start-date.end-date {
            background-color: #238781 !important;
            color: white !important;
            font-weight: bold !important;
        }

        .daterangepicker .ranges li:hover {
            background-color: rgba(35, 135, 129, 0.1);
        }

        .daterangepicker .ranges li.active {
            background-color: #238781;
            color: white;
        }

        .daterangepicker .drp-buttons .btn-primary {
            background-color: #238781 !important;
            border-color: #238781 !important;
        }

        .daterangepicker .drp-buttons .btn-primary:hover {
            background-color: #1a6661 !important;
            border-color: #1a6661 !important;
        }
    </style>
@endpush
@section('content')
    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-xxl-12 col-sm-12">
            @if (auth()->user()->role != \App\Enums\UserRole::OWNER->value)
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="ri-information-line me-2"></i>
                    <strong>Informasi:</strong> Laporan keuangan ditampilkan untuk <strong>3 bulan terakhir</strong>
                    ({{ now()->subMonths(2)->translatedFormat('F') }} - {{ now()->translatedFormat('F Y') }}).
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

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
                    @if (auth()->user()->role == \App\Enums\UserRole::OWNER->value)
                        <h5 class="card-title">Grafik Keuangan Tahunan</h5>
                    @else
                        <h5 class="card-title">Grafik Keuangan (3 Bulan Terakhir)</h5>
                    @endif
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

    <!-- Detail Pendapatan Table -->
    <div class="row gx-3">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Detail Pendapatan per Kategori & Encounter</h5>
                    @if (auth()->user()->role == \App\Enums\UserRole::OWNER->value ||
                            auth()->user()->role == \App\Enums\UserRole::ADMIN->value)
                        <div class="d-flex gap-2 align-items-center">
                            <div class="input-group" style="width: 320px;">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="ri-calendar-2-line text-primary"></i>
                                </span>
                                <input type="text" id="dateRangeFilter" class="form-control border-start-0 ps-0"
                                    placeholder="Pilih Rentang Tanggal" readonly style="cursor: pointer;">
                            </div>
                            <button type="button" class="btn btn-outline-secondary" id="resetDateFilter"
                                data-bs-toggle="tooltip" data-bs-placement="top" title="Reset Filter Tanggal">
                                <i class="ri-refresh-line"></i>
                            </button>
                        </div>
                    @else
                        <div class="alert alert-info mb-0 py-2 px-3" style="font-size: 0.875rem;">
                            <i class="ri-information-line me-1"></i>
                            Menampilkan data 3 bulan terakhir
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tableDetailPendapatan" class="table table-striped table-hover align-middle">
                            <thead class="table-success">
                                <tr>
                                    <th>#</th>
                                    <th>No. Encounter</th>
                                    <th>Pasien</th>
                                    <th>Tanggal</th>
                                    <th>Tipe</th>
                                    <th class="text-end">Pendapatan Tindakan</th>
                                    <th class="text-end">Pendapatan Farmasi</th>
                                    <th class="text-end">Total Pendapatan</th>
                                    <th class="text-center">Status Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX/DataTables -->
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="5" class="text-end">TOTAL:</td>
                                    <td class="text-end" id="totalTindakan">-</td>
                                    <td class="text-end" id="totalFarmasi">-</td>
                                    <td class="text-end" id="totalPendapatan">-</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
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

        // Initialize DataTable for Detail Pendapatan
        $(document).ready(function() {
            console.log('Initializing DataTable...');

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initialize date range picker
            var isOwner = {{ auth()->user()->role == \App\Enums\UserRole::OWNER->value ? 'true' : 'false' }};
            var isAdmin = {{ auth()->user()->role == \App\Enums\UserRole::ADMIN->value ? 'true' : 'false' }};
            var isSuperAdmin = isOwner || isAdmin;
            var threeMonthsAgo = moment().subtract(2, 'months').startOf('month');
            var today = moment();

            // Only initialize date range picker for Super Admin (Owner or Admin)
            if (isSuperAdmin) {
                $('#dateRangeFilter').daterangepicker({
                    autoUpdateInput: false,
                    opens: 'left',
                    drops: 'down',
                    locale: {
                        cancelLabel: 'Clear',
                        format: 'DD/MM/YYYY',
                        separator: ' - ',
                        applyLabel: 'Terapkan',
                        cancelLabel: 'Batal',
                        fromLabel: 'Dari',
                        toLabel: 'Sampai',
                        customRangeLabel: 'Custom',
                        weekLabel: 'W',
                        daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                        monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
                            'Agustus',
                            'September', 'Oktober', 'November', 'Desember'
                        ],
                        firstDay: 1
                    },
                    minDate: isOwner ? undefined : threeMonthsAgo,
                    maxDate: today,
                    ranges: isOwner ? {
                        'Hari Ini': [moment(), moment()],
                        'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                        'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                        'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment()
                            .subtract(1,
                                'month').endOf('month')
                        ],
                        '3 Bulan Terakhir': [moment().subtract(2, 'months').startOf('month'), moment()],
                        '6 Bulan Terakhir': [moment().subtract(5, 'months').startOf('month'), moment()],
                        'Tahun Ini': [moment().startOf('year'), moment()]
                    } : {
                        'Hari Ini': [moment(), moment()],
                        'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                        'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                        'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment()
                            .subtract(1,
                                'month').endOf('month')
                        ],
                        '3 Bulan Terakhir': [threeMonthsAgo, moment()]
                    }
                });

                $('#dateRangeFilter').on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(
                        'DD/MM/YYYY'));
                    table.draw();
                });

                $('#dateRangeFilter').on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                    table.draw();
                });

                $('#resetDateFilter').on('click', function() {
                    $('#dateRangeFilter').val('');
                    table.draw();
                });
            }

            var table = $('#tableDetailPendapatan').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('keuangan.detail.pendapatan') }}',
                    type: 'GET',
                    data: function(d) {
                        // Only apply date filter for Super Admin
                        if (isSuperAdmin) {
                            var dateRange = $('#dateRangeFilter').val();
                            if (dateRange) {
                                var dates = dateRange.split(' - ');
                                d.start_date = moment(dates[0], 'DD/MM/YYYY').format('YYYY-MM-DD');
                                d.end_date = moment(dates[1], 'DD/MM/YYYY').format('YYYY-MM-DD');
                            }
                        }
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables Error:', error, thrown);
                        console.error('Response:', xhr.responseText);
                        alert('Error loading data: ' + error);
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'no_encounter',
                        name: 'no_encounter'
                    },
                    {
                        data: 'pasien',
                        name: 'name_pasien'
                    },
                    {
                        data: 'tanggal',
                        name: 'created_at'
                    },
                    {
                        data: 'tipe',
                        name: 'type',
                        render: function(data) {
                            const types = {
                                1: '<span class="badge bg-info">Rawat Jalan</span>',
                                2: '<span class="badge bg-primary">Rawat Inap</span>',
                                3: '<span class="badge bg-danger">IGD</span>'
                            };
                            return types[data] || '-';
                        }
                    },
                    {
                        data: 'pendapatan_tindakan',
                        name: 'total_bayar_tindakan',
                        className: 'text-end',
                        render: function(data) {
                            return 'Rp ' + parseInt(data || 0).toLocaleString('id-ID');
                        }
                    },
                    {
                        data: 'pendapatan_farmasi',
                        name: 'total_bayar_resep',
                        className: 'text-end',
                        render: function(data) {
                            return 'Rp ' + parseInt(data || 0).toLocaleString('id-ID');
                        }
                    },
                    {
                        data: 'total_pendapatan',
                        name: 'total_pendapatan',
                        className: 'text-end fw-bold',
                        render: function(data) {
                            return 'Rp ' + parseInt(data || 0).toLocaleString('id-ID');
                        }
                    },
                    {
                        data: 'status_bayar',
                        name: 'status_bayar',
                        className: 'text-center',
                        orderable: false
                    }
                ],
                order: [
                    [3, 'desc']
                ],
                pageLength: 25,
                language: {
                    "sProcessing": "Sedang memproses...",
                    "sLengthMenu": "Tampilkan _MENU_ data",
                    "sZeroRecords": "Tidak ditemukan data yang sesuai",
                    "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                    "sInfoFiltered": "(disaring dari _MAX_ data keseluruhan)",
                    "sSearch": "Cari:",
                    "oPaginate": {
                        "sFirst": "Pertama",
                        "sPrevious": "Sebelumnya",
                        "sNext": "Selanjutnya",
                        "sLast": "Terakhir"
                    }
                },
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();

                    // Calculate totals
                    var totalTindakan = api.column(5, {
                        page: 'current'
                    }).data().reduce(function(a, b) {
                        return parseInt(a) + parseInt(b || 0);
                    }, 0);

                    var totalFarmasi = api.column(6, {
                        page: 'current'
                    }).data().reduce(function(a, b) {
                        return parseInt(a) + parseInt(b || 0);
                    }, 0);

                    var totalPendapatan = totalTindakan + totalFarmasi;

                    // Update footer
                    $('#totalTindakan').html('Rp ' + totalTindakan.toLocaleString('id-ID'));
                    $('#totalFarmasi').html('Rp ' + totalFarmasi.toLocaleString('id-ID'));
                    $('#totalPendapatan').html('Rp ' + totalPendapatan.toLocaleString('id-ID'));
                }
            });
        });
    </script>
@endpush
