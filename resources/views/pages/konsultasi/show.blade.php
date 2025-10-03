@extends('layouts.app')
@section('title', 'Detail Konsultasi Spesialis')
@push('style')
<link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-12 col-lg-8">
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detail Konsultasi</h5>
        <div>
          <a href="{{ route('konsultasi.print', $consultation->id) }}" target="_blank" class="btn btn-sm btn-outline-info">Cetak</a>
          <a href="{{ route('konsultasi.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
        </div>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <small class="text-muted">Tanggal Dibuat</small>
            <div>{{ $consultation->created_at->format('d M Y H:i') }}</div>
          </div>
          <div class="col-md-6">
            <small class="text-muted">Status</small>
            <div><span class="badge bg-secondary">{{ ucfirst($consultation->status) }}</span></div>
          </div>
          <div class="col-md-6">
            <small class="text-muted">Rekam Medis</small>
            <div><strong>{{ $consultation->encounter->rekam_medis ?? '-' }}</strong></div>
          </div>
          <div class="col-md-6">
            <small class="text-muted">Nama Pasien</small>
            <div>{{ $consultation->encounter->name_pasien ?? '-' }}</div>
          </div>
          <div class="col-md-6">
            <small class="text-muted">Spesialis</small>
            <div>{{ $consultation->specialist->name ?? '-' }}</div>
          </div>
          <div class="col-md-6">
            <small class="text-muted">Dokter Ditugasi</small>
            <div>{{ $consultation->assignedDoctor->name ?? '-' }}</div>
          </div>
          <div class="col-md-6">
            <small class="text-muted">Dijadwalkan</small>
            <div>{{ $consultation->scheduled_at ? $consultation->scheduled_at->format('d M Y H:i') : '-' }}</div>
          </div>
          <div class="col-12">
            <small class="text-muted">Alasan/Indikasi</small>
            <div class="border rounded p-2">{!! nl2br(e($consultation->reason)) !!}</div>
          </div>
          <div class="col-12">
            <small class="text-muted">Hasil Konsultasi</small>
            <div class="border rounded p-2">{!! nl2br(e($consultation->result_notes ?? '-')) !!}</div>
          </div>
        </div>
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
