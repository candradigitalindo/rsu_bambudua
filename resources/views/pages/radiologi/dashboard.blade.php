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
              <div class="fs-4 fw-bold">{{ $stats['today'] ?? 0 }}</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="p-3 bg-light border rounded">
              <div class="text-muted small">Diproses</div>
              <div class="fs-4 fw-bold">{{ $stats['processing'] ?? 0 }}</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="p-3 bg-light border rounded">
              <div class="text-muted small">Selesai</div>
              <div class="fs-4 fw-bold">{{ $stats['completed'] ?? 0 }}</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="p-3 bg-light border rounded">
              <div class="text-muted small">Tertunda</div>
              <div class="fs-4 fw-bold">{{ $stats['requested'] ?? 0 }}</div>
            </div>
          </div>
        </div>
        <hr>
        <h6 class="mb-2">Permintaan Terbaru</h6>
        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Pasien</th>
                <th>Pemeriksaan</th>
                <th>Dokter</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recent ?? [] as $r)
              <tr>
                <td>{{ optional($r->created_at)->format('d M Y H:i') }}</td>
                <td>{{ $r->pasien->name ?? '-' }}<div class="small text-muted">{{ $r->pasien->rekam_medis ?? '' }}</div></td>
                <td>{{ optional($r->jenis)->name }}</td>
                <td>{{ optional($r->dokter)->name ?? '-' }}</td>
                <td><span class="badge bg-secondary">{{ ucfirst($r->status) }}</span></td>
              </tr>
              @empty
              <tr><td colspan="5" class="text-muted">Belum ada data.</td></tr>
              @endforelse
            </tbody>
          </table>
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
