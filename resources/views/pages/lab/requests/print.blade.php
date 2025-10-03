@extends('layouts.app')
@section('title','Cetak Hasil Laboratorium')
@push('style')
<style>
  @media print {
    .no-print { display: none !important; }
  }
  .report-header { border-bottom: 2px solid #000; margin-bottom: 12px; padding-bottom: 8px; }
  .small-text { font-size: 12px; color: #555; }
  .table td, .table th { vertical-align: top; }
</style>
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-12 col-lg-10">
    <div class="d-flex justify-content-between align-items-center report-header">
      <div>
        <h5 class="mb-0">Hasil Pemeriksaan Laboratorium</h5>
        <div class="small-text">Tanggal: {{ $req->completed_at?->format('d M Y H:i') ?? $req->created_at->format('d M Y H:i') }}</div>
      </div>
      <div class="no-print">
        <a href="{{ route('lab.requests.show', $req->id) }}" class="btn btn-sm btn-secondary">Kembali</a>
        <button class="btn btn-sm btn-primary" onclick="window.print()"><i class="ri-printer-line"></i> Cetak</button>
      </div>
    </div>
    <div class="mb-3">
      <div class="row g-2">
        <div class="col-md-6">
          <small class="text-muted">RM</small>
          <div>{{ $req->encounter->rekam_medis ?? '-' }}</div>
        </div>
        <div class="col-md-6">
          <small class="text-muted">Pasien</small>
          <div>{{ $req->encounter->name_pasien ?? '-' }}</div>
        </div>
        <div class="col-md-6">
          <small class="text-muted">Status</small>
          <div>{{ ucfirst($req->status) }}</div>
        </div>
        <div class="col-md-6">
          <small class="text-muted">Total Biaya</small>
          <div>{{ 'Rp ' . number_format($req->total_charge,0,',','.') }}</div>
        </div>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead>
          <tr>
            <th style="width:30%">Pemeriksaan</th>
            <th>Hasil</th>
          </tr>
        </thead>
        <tbody>
          @foreach($req->items as $it)
          <tr>
            <td>
              <div class="fw-semibold">{{ $it->test_name }}</div>
            </td>
            <td>
              @if(is_array($it->result_payload) && count($it->result_payload))
                <dl class="row mb-0">
                  @foreach($it->result_payload as $k=>$v)
                    <dt class="col-sm-4 small-text">{{ str_replace('_',' ', ucfirst($k)) }}</dt>
                    <dd class="col-sm-8">{{ $v }}</dd>
                  @endforeach
                </dl>
              @else
                <div class="row g-2">
                  <div class="col-md-4"><div><small class="text-muted">Nilai</small></div><div>{{ $it->result_value ?? '-' }}</div></div>
                  <div class="col-md-3"><div><small class="text-muted">Satuan</small></div><div>{{ $it->result_unit ?? '-' }}</div></div>
                  <div class="col-md-5"><div><small class="text-muted">Rujukan</small></div><div>{{ $it->result_reference ?? '-' }}</div></div>
                </div>
              @endif
              @if($it->result_notes)
                <div class="mt-1"><small class="text-muted">Catatan:</small> {{ $it->result_notes }}</div>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
