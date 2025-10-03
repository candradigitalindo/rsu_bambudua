@extends('layouts.app')
@section('title','Laporan Kas/Bank')
@section('content')
<div class="card mb-3">
  <div class="card-header">
    <h5 class="card-title">Laporan Kas/Bank</h5>
  </div>
  <div class="card-body">
    <form method="GET" action="{{ route('keuangan.laporan.kas-bank') }}" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label">Tanggal Mulai</label>
        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">Tanggal Selesai</label>
        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">Kas/Bank (opsional)</label>
        <input type="text" name="account" value="{{ request('account') }}" class="form-control" placeholder="Nama kas/bank">
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-fill">Terapkan</button>
        <a class="btn btn-outline-danger" href="{{ route('keuangan.laporan.kas-bank.pdf') . '?' . http_build_query(request()->query()) }}">PDF</a>
        <a class="btn btn-outline-success" href="{{ route('keuangan.laporan.kas-bank.excel') . '?' . http_build_query(request()->query()) }}">Excel</a>
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
            <th>Tanggal</th>
            <th>Akun</th>
            <th>Deskripsi</th>
            <th class="text-end">Debit</th>
            <th class="text-end">Kredit</th>
            <th class="text-end">Saldo</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="6" class="text-center text-muted">Belum ada data. Silakan atur filter lalu tekan Terapkan.</td>
          </tr>
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
