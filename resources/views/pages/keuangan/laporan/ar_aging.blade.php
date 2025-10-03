@extends('layouts.app')
@section('title','Laporan AR Aging')
@section('content')
<div class="card mb-3">
  <div class="card-header"><h5 class="card-title">Laporan AR Aging</h5></div>
  <div class="card-body">
    <form method="GET" action="{{ route('keuangan.laporan.ar-aging') }}" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label">Tanggal Mulai</label>
        <input type="date" name="start_date" value="{{ request('start_date', $filters['start_date'] ?? '') }}" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">Tanggal Selesai</label>
        <input type="date" name="end_date" value="{{ request('end_date', $filters['end_date'] ?? '') }}" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">Payer (opsional)</label>
        <input type="text" name="payer" value="{{ request('payer', $filters['payer'] ?? '') }}" class="form-control" placeholder="BPJS/Asuransi/Umum">
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-primary flex-fill" type="submit">Terapkan</button>
        <a class="btn btn-outline-danger" href="{{ route('keuangan.laporan.ar-aging.pdf') . '?' . http_build_query(request()->query()) }}">PDF</a>
        <a class="btn btn-outline-success" href="{{ route('keuangan.laporan.ar-aging.excel') . '?' . http_build_query(request()->query()) }}">Excel</a>
      </div>
    </form>
  </div>
</div>
<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered align-middle m-0">
        <thead>
          <tr>
            <th>Payer</th>
            <th class="text-end">0-30</th>
            <th class="text-end">31-60</th>
            <th class="text-end">61-90</th>
            <th class="text-end">>90</th>
            <th class="text-end">Total</th>
          </tr>
        </thead>
        <tbody>
          @php $hasData = isset($rows) && count($rows) > 0; @endphp
          @if($hasData)
            @foreach($rows as $payer => $d)
              <tr>
                <td>{{ $payer }}</td>
                <td class="text-end">{{ number_format($d['0_30'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($d['31_60'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($d['61_90'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($d['90p'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($d['total'] ?? 0, 0, ',', '.') }}</td>
              </tr>
            @endforeach
            <tr class="table-light">
              <th>Total</th>
              <th class="text-end">{{ number_format($overall['0_30'] ?? 0, 0, ',', '.') }}</th>
              <th class="text-end">{{ number_format($overall['31_60'] ?? 0, 0, ',', '.') }}</th>
              <th class="text-end">{{ number_format($overall['61_90'] ?? 0, 0, ',', '.') }}</th>
              <th class="text-end">{{ number_format($overall['90p'] ?? 0, 0, ',', '.') }}</th>
              <th class="text-end">{{ number_format($overall['total'] ?? 0, 0, ',', '.') }}</th>
            </tr>
          @else
            <tr><td colspan="6" class="text-center text-muted">Belum ada data.</td></tr>
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
      });
    </script>
@endpush
