@extends('layouts.app')
@section('title','Detail Asuhan Keperawatan')
@push('style')
<link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-12 col-lg-8">
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detail Asuhan</h5>
        <div>
          <a href="{{ route('keperawatan.print', $record->id) }}" target="_blank" class="btn btn-sm btn-outline-info">Cetak</a>
          <a href="{{ route('keperawatan.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
        </div>
      </div>
      <div class="card-body">
        <div class="row g-2">
          <div class="col-md-6"><small class="text-muted">Tanggal</small><div>{{ $record->created_at->format('d M Y H:i') }}</div></div>
          <div class="col-md-6"><small class="text-muted">Shift</small><div>{{ $record->shift ?? '-' }}</div></div>
          <div class="col-md-6"><small class="text-muted">RM</small><div>{{ $record->encounter->rekam_medis ?? '-' }}</div></div>
          <div class="col-md-6"><small class="text-muted">Pasien</small><div>{{ $record->encounter->name_pasien ?? '-' }}</div></div>
          <div class="col-md-6"><small class="text-muted">Perawat</small><div>{{ $record->nurse->name ?? '-' }}</div></div>
        </div>
        <hr/>
        <h6>Tanda Vital</h6>
        <div class="row g-2">
          <div class="col-md-2">SBP: {{ $record->systolic ?? '-' }}</div>
          <div class="col-md-2">DBP: {{ $record->diastolic ?? '-' }}</div>
          <div class="col-md-2">HR: {{ $record->heart_rate ?? '-' }}</div>
          <div class="col-md-2">RR: {{ $record->resp_rate ?? '-' }}</div>
          <div class="col-md-2">Temp: {{ $record->temperature ?? '-' }}</div>
          <div class="col-md-2">SpO2: {{ $record->spo2 ?? '-' }}</div>
        </div>
        <div class="row g-2 mt-1">
          <div class="col-md-2">Nyeri: {{ $record->pain_scale ?? '-' }}</div>
        </div>
        <hr/>
        <div class="mb-2">
          <small class="text-muted">Diagnosa Keperawatan</small>
          <div class="border rounded p-2">{!! nl2br(e($record->nursing_diagnosis ?? '-')) !!}</div>
        </div>
        <div class="mb-2">
          <small class="text-muted">Intervensi</small>
          <div class="border rounded p-2">{!! nl2br(e($record->interventions ?? '-')) !!}</div>
        </div>
        <div class="mb-2">
          <small class="text-muted">Evaluasi</small>
          <div class="border rounded p-2">{!! nl2br(e($record->evaluation_notes ?? '-')) !!}</div>
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
