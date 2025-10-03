@extends('layouts.app')

@section('title', 'Input Hasil Radiologi')
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
<div class="row">
  <div class="col-12 col-lg-8">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Input Hasil Radiologi</h5>
        <a href="{{ route('radiologi.requests.show', $req->id) }}" class="btn btn-light btn-sm">Kembali</a>
      </div>
      <div class="card-body">
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="m-0">
              @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="mb-3">
          <div class="text-muted small">Pasien</div>
          <div class="fw-semibold">{{ $req->pasien->rekam_medis ?? '-' }} — {{ $req->pasien->name ?? '-' }}</div>
        </div>
        <div class="mb-3">
          <div class="text-muted small">Pemeriksaan</div>
          <div class="fw-semibold">{{ $req->jenis->name ?? '-' }}</div>
        </div>

        <form method="POST" action="{{ route('radiologi.requests.results.store', $req->id) }}" enctype="multipart/form-data">
          @csrf
          <div class="mb-3">
            <label class="form-label">Temuan (Findings)</label>
            <textarea name="findings" class="form-control" rows="6" required>{{ old('findings') }}</textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Kesimpulan (Impression)</label>
            <textarea name="impression" class="form-control" rows="4" required>{{ old('impression') }}</textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Lampiran (opsional)</label>
            <input type="file" name="attachments[]" class="form-control" multiple>
            <div class="form-text">Anda bisa mengunggah beberapa file (maks 10MB per file).</div>
          </div>
          <div class="d-flex gap-2">
            <a href="{{ route('radiologi.requests.show', $req->id) }}" class="btn btn-light">Batal</a>
            <button type="submit" class="btn btn-success">Simpan Hasil & Selesaikan</button>
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
