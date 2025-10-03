@extends('layouts.app')
@section('title','Laporan Payor Mix')
@section('content')
<div class="card mb-3">
  <div class="card-header"><h5 class="card-title">Laporan Payor Mix</h5></div>
  <div class="card-body">
    <form method="GET" action="{{ route('keuangan.laporan.payor-mix') }}" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label">Tanggal Mulai</label>
        <input type="date" name="start_date" value="{{ request('start_date', $filters['start_date'] ?? '') }}" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">Tanggal Selesai</label>
        <input type="date" name="end_date" value="{{ request('end_date', $filters['end_date'] ?? '') }}" class="form-control">
      </div>
      <div class="col-md-6 d-flex gap-2">
        <button class="btn btn-primary flex-fill" type="submit">Terapkan</button>
        <a class="btn btn-outline-danger" href="{{ route('keuangan.laporan.payor-mix.pdf') . '?' . http_build_query(request()->query()) }}">PDF</a>
        <a class="btn btn-outline-success" href="{{ route('keuangan.laporan.payor-mix.excel') . '?' . http_build_query(request()->query()) }}">Excel</a>
      </div>
    </form>
  </div>
</div>
<div class="card">
  <div class="card-body">
    <div id="payorMixChart" style="height: 320px; margin-bottom: 16px;"></div>
    <div class="table-responsive">
      <table class="table table-bordered align-middle m-0">
        <thead>
          <tr>
            <th>Payor</th>
            <th class="text-end">Pendapatan</th>
            <th class="text-end">Persentase</th>
          </tr>
        </thead>
        <tbody>
          @php $hasData = isset($mix) && count($mix) > 0; @endphp
          @if($hasData)
            @foreach($mix as $payer => $amt)
              <tr>
                <td>{{ $payer }}</td>
                <td class="text-end">{{ number_format($amt, 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($percentages[$payer] ?? 0, 2, ',', '.') }}%</td>
              </tr>
            @endforeach
            <tr class="table-light">
              <th>Total</th>
              <th class="text-end">{{ number_format($total ?? 0, 0, ',', '.') }}</th>
              <th class="text-end">{{ ($total ?? 0) > 0 ? '100.00%' : '0.00%' }}</th>
            </tr>
          @else
            <tr><td colspan="3" class="text-center text-muted">Belum ada data.</td></tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/daterange/daterange.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bs5.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bs5-custom.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/buttons/dataTables.bs5-custom.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <script src="{{ asset('vendor/daterange/daterange.js') }}"></script>
    <script src="{{ asset('vendor/daterange/custom-daterange.js') }}"></script>
    <script src="{{ asset('vendor/apex/apexcharts.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/custom/custom-datatables.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function(){
        const table = document.querySelector('.table');
        if (table && window.jQuery && jQuery.fn.DataTable) {
          jQuery(table).DataTable({ paging: true, searching: true });
        }
        @if(!empty($chart['categories'] ?? []) && !empty($chart['series'] ?? []))
        try {
          const chartOptions = {
            chart: { type: 'bar', height: 320, toolbar: { show: false } },
            series: @json($chart['series'] ?? []),
            xaxis: { categories: @json($chart['categories'] ?? []) },
            tooltip: { y: { formatter: (v) => 'Rp ' + (v||0).toLocaleString('id-ID') } },
            dataLabels: { enabled: false },
            grid: { borderColor: '#eee' },
            colors: ['#238781']
          };
          const el = document.querySelector('#payorMixChart');
          if (el && window.ApexCharts) {
            const chart = new ApexCharts(el, chartOptions);
            chart.render();
          }
        } catch (e) {}
        @endif
      });
    </script>
@endpush
