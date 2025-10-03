@extends('layouts.app')

@section('title', 'Dashboard Radiologi')
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Dashboard Radiologi</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <div class="p-3 bg-light border rounded">
              <div class="text-muted small">Permintaan (Hari Ini)</div>
              <div class="fs-4 fw-bold">-</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="p-3 bg-light border rounded">
              <div class="text-muted small">Diproses</div>
              <div class="fs-4 fw-bold">-</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="p-3 bg-light border rounded">
              <div class="text-muted small">Selesai</div>
              <div class="fs-4 fw-bold">-</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="p-3 bg-light border rounded">
              <div class="text-muted small">Tertunda</div>
              <div class="fs-4 fw-bold">-</div>
            </div>
          </div>
        </div>
        <hr>
        <div class="text-muted">Grafik & Ringkasan akan ditampilkan di sini.</div>
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
