@extends('layouts.app')
@section('title','Laporan P&L per Cost Center')
@section('content')
<div class="card mb-3">
  <div class="card-header"><h5 class="card-title">Laporan P&L per Cost Center</h5></div>
  <div class="card-body">
    <form method="GET" action="{{ route('keuangan.laporan.pnl-cost-center') }}" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label">Tanggal Mulai</label>
        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">Tanggal Selesai</label>
        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">Cost Center (opsional)</label>
        <input type="text" name="cost_center" value="{{ request('cost_center') }}" class="form-control" placeholder="Unit/Instalasi">
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-primary flex-fill" type="submit">Terapkan</button>
        <a class="btn btn-outline-danger" href="{{ route('keuangan.laporan.pnl-cost-center.pdf') . '?' . http_build_query(request()->query()) }}">PDF</a>
        <a class="btn btn-outline-success" href="{{ route('keuangan.laporan.pnl-cost-center.excel') . '?' . http_build_query(request()->query()) }}">Excel</a>
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
            <th>Cost Center</th>
            <th class="text-end">Pendapatan</th>
            <th class="text-end">Beban</th>
            <th class="text-end">Laba/Rugi</th>
          </tr>
        </thead>
        <tbody>
          <tr><td colspan="4" class="text-center text-muted">Belum ada data.</td></tr>
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
