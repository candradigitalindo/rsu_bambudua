@extends('layouts.app')
@section('title', 'Tambah Satuan')
@push('style')
<link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-12 col-lg-8">
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Tambah Satuan</h5>
        <a href="{{ route('units.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
      </div>
      <div class="card-body">
        <form action="{{ route('units.store') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Singkatan</label>
            <input type="text" class="form-control @error('abbrev') is-invalid @enderror" name="abbrev" value="{{ old('abbrev') }}">
            @error('abbrev')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="active" id="active" {{ old('active', true) ? 'checked' : '' }}>
            <label class="form-check-label" for="active">Aktif</label>
          </div>
          <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('units.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
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
