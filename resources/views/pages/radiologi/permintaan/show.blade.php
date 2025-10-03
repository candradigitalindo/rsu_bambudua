@extends('layouts.app')

@section('title', 'Detail Permintaan Radiologi')
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
<div class="row">
  <div class="col-12 col-lg-8">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Detail Permintaan Radiologi</h5>
        <a href="{{ route('radiologi.requests.index') }}" class="btn btn-light btn-sm">Kembali</a>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="text-muted small">No. RM</div>
            <div class="fw-semibold">{{ $req->pasien->rekam_medis ?? '-' }}</div>
          </div>
          <div class="col-md-6">
            <div class="text-muted small">Nama Pasien</div>
            <div class="fw-semibold">{{ $req->pasien->name ?? '-' }}</div>
          </div>
          <div class="col-md-6">
            <div class="text-muted small">Pemeriksaan</div>
            <div class="fw-semibold">{{ $req->jenis->name ?? '-' }}</div>
          </div>
          <div class="col-md-3">
            <div class="text-muted small">Tarif</div>
            <div class="fw-semibold">{{ 'Rp ' . number_format($req->price ?? 0, 0, ',', '.') }}</div>
          </div>
          <div class="col-md-3">
            <div class="text-muted small">Status</div>
            <div><span class="badge bg-secondary">{{ ucfirst($req->status ?? '-') }}</span></div>
          </div>
          <div class="col-md-6">
            <div class="text-muted small">Dokter Pengirim</div>
            <div class="fw-semibold">{{ $req->dokter->name ?? '-' }}</div>
          </div>
          <div class="col-md-6">
            <div class="text-muted small">Tanggal Permintaan</div>
            <div class="fw-semibold">{{ optional($req->created_at)->format('d M Y H:i') }}</div>
          </div>
          <div class="col-12">
            <div class="text-muted small">Catatan</div>
            <div class="fw-semibold">{{ $req->notes ?? '-' }}</div>
          </div>
        </div>
        <hr>
        <div>
          <div class="text-muted small mb-1">Hasil Terbaru</div>
          @if(!empty($latestResult))
            <div class="mb-2">
              <div class="small text-muted">Dilaporkan</div>
              <div class="fw-semibold">{{ optional($latestResult->reported_at)->format('d M Y H:i') }} oleh {{ optional($latestResult->reporter)->name ?? '-' }}</div>
            </div>
            <div class="mb-2">
              <div class="small text-muted">Temuan (Findings)</div>
              <div>{{ $latestResult->findings }}</div>
            </div>
            <div class="mb-2">
              <div class="small text-muted">Kesimpulan (Impression)</div>
              <div>{{ $latestResult->impression }}</div>
            </div>
            @if(is_array($latestResult->files) && count($latestResult->files))
              <div class="mb-2">
                <div class="small text-muted">Lampiran</div>
                <ul class="mb-0">
                  @foreach($latestResult->files as $file)
                    <li><a href="{{ asset('storage/' . $file) }}" target="_blank">{{ basename($file) }}</a></li>
                  @endforeach
                </ul>
              </div>
            @endif
          @else
            <div class="text-muted">Belum ada hasil.</div>
          @endif
        </div>
      </div>
      <div class="card-footer d-flex gap-2 align-items-center flex-wrap">
        <a href="{{ route('radiologi.requests.index') }}" class="btn btn-light">Kembali</a>
        @if(session('success'))
          <span class="text-success">{{ session('success') }}</span>
        @endif
        @if(session('error'))
          <span class="text-danger">{{ session('error') }}</span>
        @endif
        @php($st = $req->status)
        @if($st === 'requested')
          <a href="{{ route('radiologi.requests.schedule.create', $req->id) }}" class="btn btn-outline-primary">Jadwalkan</a>
          <form method="POST" action="{{ route('radiologi.requests.status', $req->id) }}">
            @csrf
            <input type="hidden" name="status" value="canceled">
            <button type="submit" class="btn btn-outline-danger">Batalkan Permintaan</button>
          </form>
        @elseif($st === 'processing')
          <a href="{{ route('radiologi.requests.results.edit', $req->id) }}" class="btn btn-outline-success">Input Hasil</a>
        @endif
        <button class="btn btn-outline-secondary" disabled>Cetak (segera)</button>
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
