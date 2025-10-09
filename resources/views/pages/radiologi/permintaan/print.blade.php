@extends('layouts.print')
@section('title','Hasil Radiologi')
@push('style')
<style>
  .box { border: 1px solid #333; padding: 8px; margin-top: 10px; }
  .label { color: #666; }
  table { width: 100%; border-collapse: collapse; margin-top: 10px; }
  th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
</style>
@endpush
@push('scripts')
@if(request()->get('auto'))
<script>
  window.addEventListener('load', function(){ setTimeout(function(){ window.print(); }, 100); });
</script>
@endif
@endpush
@section('content')
  <h5 class="mb-1">Hasil Radiologi</h5>
  <div class="mb-2 small-text">Tanggal: {{ optional($req->created_at)->format('d M Y H:i') }}</div>
  <div class="mb-2">
    <div><span class="label">Pasien:</span> {{ $req->pasien->rekam_medis ?? '' }} - {{ $req->pasien->name ?? '' }}</div>
    <div><span class="label">Pemeriksaan:</span> {{ optional($req->jenis)->name }}</div>
    <div><span class="label">Dokter Pengirim:</span> {{ optional($req->dokter)->name }}</div>
    <div><span class="label">Status:</span> {{ ucfirst($req->status) }}</div>
  </div>
  @if($latest)
    <div class="box">
      <div><strong>Findings</strong></div>
      <div>{{ $latest->findings }}</div>
    </div>
    <div class="box">
      <div><strong>Impression</strong></div>
      <div>{{ $latest->impression }}</div>
    </div>
  @else
    <div class="box">Belum ada hasil untuk permintaan ini.</div>
  @endif
@endsection
