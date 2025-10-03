@extends('layouts.app')
@section('title','Edit Alat Medis')
@push('style')
  <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/daterange/daterange.css') }}">
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-12">
    <div class="card mb-3">
      <div class="card-header">
        <h5 class="card-title mb-0">Edit Alat Medis</h5>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('inventory.equipment.update', $equipment['id']) }}" class="row g-3">
          @csrf
          @method('PUT')
          <div class="col-md-6">
            <label class="form-label">Nama</label>
            <input name="name" class="form-control" value="{{ $equipment['name'] }}" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Kategori</label>
            <input name="category" class="form-control" value="{{ $equipment['category'] }}">
          </div>
          <div class="col-md-4">
            <label class="form-label">Serial Number</label>
            <input name="serial_number" class="form-control" value="{{ $equipment['serial_number'] }}">
          </div>
          <div class="col-md-4">
            <label class="form-label">Asset Tag</label>
            <input name="asset_tag" class="form-control" value="{{ $equipment['asset_tag'] }}">
          </div>
          <div class="col-md-4">
            <label class="form-label">Lokasi</label>
            <input name="location" class="form-control" value="{{ $equipment['location'] }}">
          </div>
          <div class="col-md-4">
            <label class="form-label">Vendor</label>
            <input name="vendor" class="form-control" value="{{ $equipment['vendor'] }}">
          </div>
          <div class="col-md-4">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
              @php $status = $equipment['status']; @endphp
              <option value="available" @selected($status==='available')>Tersedia</option>
              <option value="in_use" @selected($status==='in_use')>Dipakai</option>
              <option value="maintenance" @selected($status==='maintenance')>Maintenance</option>
              <option value="repair" @selected($status==='repair')>Perbaikan</option>
              <option value="decommissioned" @selected($status==='decommissioned')>Dihapus</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Tanggal Pembelian</label>
            <input type="date" name="purchase_date" class="form-control" value="{{ $equipment['purchase_date'] }}">
          </div>
          <div class="col-md-4">
            <label class="form-label">Garansi Berakhir</label>
            <input type="date" name="warranty_expiry" class="form-control" value="{{ $equipment['warranty_expiry'] }}">
          </div>
          <div class="col-md-4">
            <label class="form-label">Kalibrasi Terakhir</label>
            <input type="date" name="last_calibration_date" class="form-control" value="{{ $equipment['last_calibration_date'] }}">
          </div>
          <div class="col-md-4">
            <label class="form-label">Kalibrasi Berikutnya</label>
            <input type="date" name="next_calibration_due" class="form-control" value="{{ $equipment['next_calibration_due'] }}">
          </div>
          <div class="col-12">
            <label class="form-label">Catatan</label>
            <textarea name="notes" class="form-control" rows="3">{{ $equipment['notes'] }}</textarea>
          </div>
          <div class="col-12 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Simpan</button>
            <a class="btn btn-secondary" href="{{ route('inventory.equipment.show', $equipment['id']) }}">Batal</a>
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