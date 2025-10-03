@extends('layouts.app')
@section('title','Tambah Alat Medis')
@push('style')
  <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/daterange/daterange.css') }}">
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-12">
    <div class="card mb-3">
      <div class="card-header">
        <h5 class="card-title mb-0">Tambah Alat Medis</h5>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('inventory.equipment.store') }}" class="row g-3">
          @csrf
          <div class="col-md-6">
            <label class="form-label">Nama</label>
            <input name="name" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Kategori</label>
            <input name="category" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Serial Number</label>
            <input name="serial_number" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Asset Tag</label>
            <input name="asset_tag" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Lokasi</label>
            <input name="location" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Vendor</label>
            <input name="vendor" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
              <option value="available">Tersedia</option>
              <option value="in_use">Dipakai</option>
              <option value="maintenance">Maintenance</option>
              <option value="repair">Perbaikan</option>
              <option value="decommissioned">Dihapus</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Tanggal Pembelian</label>
            <input type="date" name="purchase_date" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Garansi Berakhir</label>
            <input type="date" name="warranty_expiry" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Kalibrasi Terakhir</label>
            <input type="date" name="last_calibration_date" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Kalibrasi Berikutnya</label>
            <input type="date" name="next_calibration_due" class="form-control">
          </div>
          <div class="col-12">
            <label class="form-label">Catatan</label>
            <textarea name="notes" class="form-control" rows="3"></textarea>
          </div>
          <div class="col-12 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Simpan</button>
            <a class="btn btn-secondary" href="{{ route('inventory.equipment.index') }}">Batal</a>
          </div>
        </form>
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
  <script src="{{ asset('js/custom.js') }}"></script>
@endpush
