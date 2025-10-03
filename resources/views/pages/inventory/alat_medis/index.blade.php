@extends('layouts.app')
@section('title','Alat Medis')
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/daterange/daterange.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bs5.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bs5-custom.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/buttons/dataTables.bs5-custom.css') }}">
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-12">
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Daftar Alat Medis</h5>
        <div class="d-flex gap-2">
          <a href="{{ route('inventory.equipment.export.pdf', request()->query()) }}" class="btn btn-outline-danger btn-sm"><i class="ri-file-pdf-line"></i> PDF</a>
          <a href="{{ route('inventory.equipment.export.excel', request()->query()) }}" class="btn btn-outline-success btn-sm"><i class="ri-file-excel-2-line"></i> Excel</a>
          <a href="{{ route('inventory.equipment.create') }}" class="btn btn-primary btn-sm"><i class="ri-add-line"></i> Tambah</a>
        </div>
      </div>
      <div class="card-body">
        <form method="GET" class="row g-2 mb-3 align-items-end">
          <div class="col-md-3">
            <label class="form-label">Cari</label>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control" placeholder="Nama/Serial/Asset Tag">
          </div>
          <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="">Semua</option>
              @php $statusOpts=['available'=>'Tersedia','in_use'=>'Dipakai','maintenance'=>'Maintenance','repair'=>'Perbaikan','decommissioned'=>'Dihapus']; @endphp
              @foreach($statusOpts as $k=>$v)
                <option value="{{ $k }}" @selected(($filters['status'] ?? '')===$k)>{{ $v }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Lokasi</label>
            <input name="location" value="{{ $filters['location'] ?? '' }}" class="form-control" placeholder="IGD/OK/RI">
          </div>
          <div class="col-md-2">
            <label class="form-label">Vendor</label>
            <input name="vendor" value="{{ $filters['vendor'] ?? '' }}" class="form-control" placeholder="Vendor">
          </div>
          <div class="col-md-3">
            <label class="form-label">Kalibrasi Jatuh Tempo</label>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="calibration_due" value="1" id="cal_due" @checked(($filters['calibration_due'] ?? false))>
              <label class="form-check-label" for="cal_due">Tampilkan hanya yang due</label>
            </div>
          </div>
          <div class="col-md-3">
            <label class="form-label">Tanggal Mulai</label>
            <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">Tanggal Selesai</label>
            <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="form-control">
          </div>
          <div class="col-md-2">
            <button class="btn btn-primary w-100" type="submit"><i class="ri-filter-line"></i> Filter</button>
          </div>
        </form>

        <div class="table-responsive">
          <table class="table m-0" id="equipmentTable">
            <thead>
              <tr>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Serial</th>
                <th>Asset Tag</th>
                <th>Lokasi</th>
                <th>Status</th>
                <th>Vendor</th>
                <th>Kalibrasi Berikutnya</th>
                <th class="text-center" style="width: 140px;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($equipments as $eq)
                <tr>
                  <td>{{ $eq->name }}</td>
                  <td>{{ $eq->category }}</td>
                  <td>{{ $eq->serial_number }}</td>
                  <td>{{ $eq->asset_tag }}</td>
                  <td>{{ $eq->location }}</td>
                  <td><span class="badge bg-secondary text-uppercase">{{ $eq->status }}</span></td>
                  <td>{{ $eq->vendor }}</td>
                  <td>{{ optional($eq->next_calibration_due)->format('Y-m-d') }}</td>
                  <td class="text-center">
                    <a href="{{ route('inventory.equipment.show', $eq->id) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" class="text-center text-muted">Belum ada data.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="mt-3">
          {{ $equipments->links() }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
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
      if (window.jQuery && jQuery.fn.DataTable) {
        jQuery('#equipmentTable').DataTable({ paging: true, searching: true });
      }
    });
  </script>
@endpush
