@extends('layouts.app')

@section('title', 'Buat Permintaan Radiologi')
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
<div class="row">
  <div class="col-12 col-lg-8">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Buat Permintaan Radiologi</h5>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('radiologi.requests.store') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">Pasien</label>
            <select name="pasien_id" id="pasien_id" class="form-select js-pasien-select" data-placeholder="Cari pasien (RM, nama, NIK, no HP)"></select>
            <div class="form-text">Ketik minimal 2 karakter untuk mencari pasien.</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Pemeriksaan</label>
            <select name="pemeriksaan" class="form-select">
              <option value="">-- Pilih Pemeriksaan --</option>
              @foreach(($jenisPemeriksaan ?? []) as $item)
                <option value="{{ $item->id }}">{{ $item->name }} - {{ number_format($item->harga, 0, ',', '.') }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Dokter Pengirim</label>
            <select name="dokter_id" id="dokter_id" class="form-select js-dokter-select" data-placeholder="Cari dokter (nama/username)"></select>
            <div class="form-text">Ketik minimal 2 karakter untuk mencari dokter.</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Keterangan</label>
            <textarea name="catatan" class="form-control" rows="3"></textarea>
          </div>
          <div class="d-flex gap-2">
            <a href="{{ route('radiologi.requests.index') }}" class="btn btn-light">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Permintaan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
<script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
<script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>
<script>
$(function(){
  if ($.fn.select2) {
    $('#pasien_id').select2({
      theme: 'bootstrap-5',
      width: '100%',
      placeholder: $('#pasien_id').data('placeholder') || 'Cari pasien...',
      minimumInputLength: 2,
      ajax: {
        delay: 300,
        url: '{{ route('radiologi.patients.search') }}',
        dataType: 'json',
        data: function (params) {
          return { q: params.term };
        },
        processResults: function (data) {
          return data; // { results: [...] }
        },
        cache: true
      }
    });

    $('#dokter_id').select2({
      theme: 'bootstrap-5',
      width: '100%',
      placeholder: $('#dokter_id').data('placeholder') || 'Cari dokter...',
      minimumInputLength: 2,
      ajax: {
        delay: 300,
        url: '{{ route('radiologi.doctors.search') }}',
        dataType: 'json',
        data: function (params) {
          return { q: params.term };
        },
        processResults: function (data) {
          return data;
        },
        cache: true
      }
    });
  }
});
</script>
@endpush
