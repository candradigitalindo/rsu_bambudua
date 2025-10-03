@extends('layouts.app')
@section('title','Laporan Inventory')
@section('content')
<div class="card mb-3">
  <div class="card-header"><h5 class="card-title">Laporan Inventory</h5></div>
  <div class="card-body">
    <form method="GET" action="{{ route('keuangan.laporan.inventory') }}" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label">Tanggal Per</label>
        <input type="date" name="as_of" value="{{ request('as_of') }}" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">Kategori (opsional)</label>
        <input type="text" name="category" value="{{ request('category') }}" class="form-control" placeholder="Kategori">
      </div>
      <div class="col-md-6 d-flex gap-2">
        <button class="btn btn-primary flex-fill" type="submit">Terapkan</button>
        <a class="btn btn-outline-danger" href="{{ route('keuangan.laporan.inventory.pdf') . '?' . http_build_query(request()->query()) }}">PDF</a>
        <a class="btn btn-outline-success" href="{{ route('keuangan.laporan.inventory.excel') . '?' . http_build_query(request()->query()) }}">Excel</a>
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
            <th>Kode</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th class="text-end">Qty</th>
            <th class="text-end">Nilai</th>
            <th>Kadaluarsa</th>
          </tr>
        </thead>
        <tbody>
          <tr><td colspan="6" class="text-center text-muted">Belum ada data.</td></tr>
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
