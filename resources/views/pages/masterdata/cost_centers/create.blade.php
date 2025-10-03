@extends('layouts.app')
@section('title','Tambah Cost Center')
@push('style')
  <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-12">
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Tambah Cost Center</h5>
        <a href="{{ route('master.cost-centers.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('master.cost-centers.store') }}" class="row g-3">
          @csrf
          <div class="col-md-6">
            <label class="form-label">Nama</label>
            <input name="name" class="form-control" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Kode</label>
            <input name="code" class="form-control">
          </div>
          <div class="col-md-9">
            <label class="form-label">Deskripsi</label>
            <input name="description" class="form-control">
          </div>
          <div class="col-md-3 d-flex align-items-center gap-2">
            <div class="form-check">
              <input type="checkbox" class="form-check-input" name="is_active" id="cc_active" checked>
              <label class="form-check-label" for="cc_active">Aktif</label>
            </div>
          </div>
          <div class="col-12">
            <button class="btn btn-primary" type="submit">Simpan</button>
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
  <script src="{{ asset('js/custom.js') }}"></script>
@endpush
