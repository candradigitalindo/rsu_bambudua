@extends('layouts.app')
@section('title','Detail Permintaan Lab')
@push('style')
<link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-12 col-lg-10">
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detail Permintaan Lab</h5>
        <div>
          @if($req->status === 'completed')
          <a href="{{ route('lab.requests.print', $req->id) }}" target="_blank" class="btn btn-sm btn-success">Cetak Hasil</a>
          @endif
          <a href="{{ route('lab.requests.edit', $req->id) }}" class="btn btn-sm btn-outline-primary">Hasil/Status</a>
          <a href="{{ route('lab.requests.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
        </div>
      </div>
      <div class="card-body">
        <div class="row g-2 mb-3">
          <div class="col-md-4"><small class="text-muted">Tanggal</small><div>{{ $req->created_at->format('d M Y H:i') }}</div></div>
          <div class="col-md-4"><small class="text-muted">Status</small><div><span class="badge bg-secondary">{{ ucfirst($req->status) }}</span></div></div>
          <div class="col-md-4"><small class="text-muted">Total</small><div>{{ 'Rp ' . number_format($req->total_charge,0,',','.') }}</div></div>
        </div>
        <div class="row g-2 mb-3">
          <div class="col-md-6"><small class="text-muted">RM</small><div>{{ $req->encounter->rekam_medis ?? '-' }}</div></div>
          <div class="col-md-6"><small class="text-muted">Pasien</small><div>{{ $req->encounter->name_pasien ?? '-' }}</div></div>
        </div>
        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead><tr><th style="width:25%">Pemeriksaan</th><th>Detail Hasil</th><th style="width:10%">Harga</th></tr></thead>
            <tbody>
              @foreach($req->items as $it)
              <tr>
                <td>{{ $it->test_name }}</td>
                <td>
                  @if(is_array($it->result_payload) && count($it->result_payload))
                    <dl class="row mb-0">
                      @foreach($it->result_payload as $k=>$v)
                        <dt class="col-sm-4 text-muted small">{{ str_replace('_',' ', ucfirst($k)) }}</dt>
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
                <td>{{ 'Rp ' . number_format($it->price,0,',','.') }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @if($req->notes)
        <div class="mt-2"><small class="text-muted">Catatan:</small><div>{{ $req->notes }}</div></div>
        @endif
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
