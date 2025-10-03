@extends('layouts.app')
@section('title','Tambah Reagensia')
@push('style')
<link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-12 col-lg-8">
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Tambah Reagensia</h5>
        <a href="{{ route('lab.reagents.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
      </div>
      <div class="card-body">
        <form action="{{ route('lab.reagents.store') }}" method="POST">
          @csrf
          <div class="mb-3"><label class="form-label">Nama</label><input type="text" name="name" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Satuan</label><input type="text" name="unit" class="form-control"></div>
          <div class="mb-3"><label class="form-label">Warning Stok</label><input type="number" name="warning_stock" class="form-control" value="0"></div>
          <div class="d-flex justify-content-end gap-2"><button class="btn btn-primary" type="submit">Simpan</button></div>
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
