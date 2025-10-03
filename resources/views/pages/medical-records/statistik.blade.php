@extends('layouts.app')
@section('title')
    Statistik Medis
@endsection
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
    <div class="row gx-3">
        <div class="col-lg-7 col-12">
            <div class="card mb-3">
                <div class="card-header pb-0">
                    <h5 class="card-title">Kunjungan per Bulan ({{ date('Y') }})</h5>
                </div>
                <div class="card-body pt-0">
                    <div id="chartMonthly"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-5 col-12">
            <div class="card mb-3">
                <div class="card-header pb-0">
                    <h5 class="card-title">Top 5 Diagnosis</h5>
                </div>
                <div class="card-body pt-0">
                    <div id="chartDiagnosis"></div>
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
        // Monthly bar
        new ApexCharts(document.querySelector('#chartMonthly'), {
            chart: { type: 'bar', height: 360, toolbar: { show: false } },
            series: @json($stat['monthly']['series']),
            xaxis: { categories: @json($stat['monthly']['categories']) },
            colors: ['#238781']
        }).render();

        // Top diagnosis donut
        new ApexCharts(document.querySelector('#chartDiagnosis'), {
            chart: { type: 'donut', height: 360, toolbar: { show: false } },
            labels: @json($stat['topDiagnosis']['labels']),
            series: @json($stat['topDiagnosis']['data']),
            colors: ['#238781', '#4f9f9a', '#7bb7b3', '#a7cfcd', '#d3e7e6']
        }).render();
    </script>
@endpush
