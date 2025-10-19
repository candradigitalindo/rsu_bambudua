@extends('layouts.app')
@section('title', 'Dashboard Admin')

@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@section('content')
    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Selamat Datang, {{ auth()->user()->name }}!</h5>
                </div>

            </div>
        </div>
    </div>
    <!-- Row ends -->

    <!-- Row starts - User Statistics -->
    <div class="row gx-3">
        <div class="col-12">
            <h5 class="mb-3"><i class="ri-group-line"></i> Statistik Pengguna</h5>
        </div>
        @foreach ($userStats as $stat)
            <div class="col-lg-2 col-md-4 col-6">
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <h4 class="text-primary">{{ $stat['count'] }}</h4>
                        <p class="m-0 small text-muted">{{ $stat['label'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <!-- Row ends -->

    <div class="row gx-3">
        <div class="col-lg-8 col-12">
            <div class="row gx-3">
                <div class="col-12">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title">Pendaftaran Pasien Baru (3 Bulan Terakhir)</h5>
                        </div>
                        <div class="card-body">
                            <div id="registrationChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title">Kunjungan Pasien (3 Bulan Terakhir)</h5>
                        </div>
                        <div class="card-body">
                            <div id="encounterChart"></div>
                        </div>
                    </div>
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
                            <div class="text-center text-muted">
                                <i class="ri-newspaper-line fs-3"></i>
                                <p class="mb-0 small">Belum ada berita.</p>
                            </div>
                        @endforelse
                    </div>
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
        // Helper function to create a simple area chart
        function createAreaChart(elementId, chartData, color) {
            const options = {
                chart: {
                    type: 'area',
                    height: 300,
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                series: chartData.series,
                xaxis: {
                    type: 'category',
                    categories: chartData.categories,
                    labels: {
                        show: true,
                        rotate: -45,
                        hideOverlappingLabels: true
                    }
                },
                yaxis: {
                    title: {
                        text: 'Jumlah'
                    },
                    labels: {
                        formatter: (val) => parseInt(val)
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                colors: [color],
                grid: {
                    borderColor: '#e0e6ed',
                    strokeDashArray: 5
                },
            };
            new ApexCharts(document.querySelector(elementId), options).render();
        }

        // Render charts
        createAreaChart('#registrationChart', @json($registrationChart), '#007bff');
        createAreaChart('#encounterChart', @json($encounterChart), '#28a745');
    </script>
@endpush
