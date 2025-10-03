@extends('layouts.app')
@section('title','Dashboard Laboratorium')
@push('style')
<link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-12">
    <div class="row g-3 mb-3">
      <div class="col-md-2">
        <div class="card"><div class="card-body"><small class="text-muted">Requested</small><div class="h4 mb-0">{{ $counts['requested'] ?? 0 }}</div></div></div>
      </div>
      <div class="col-md-2">
        <div class="card"><div class="card-body"><small class="text-muted">Collected</small><div class="h4 mb-0">{{ $counts['collected'] ?? 0 }}</div></div></div>
      </div>
      <div class="col-md-2">
        <div class="card"><div class="card-body"><small class="text-muted">Processing</small><div class="h4 mb-0">{{ $counts['processing'] ?? 0 }}</div></div></div>
      </div>
      <div class="col-md-3">
        <div class="card"><div class="card-body"><small class="text-muted">Completed</small><div class="h4 mb-0">{{ $counts['completed'] ?? 0 }}</div></div></div>
      </div>
      <div class="col-md-3">
        <div class="card"><div class="card-body"><small class="text-muted">Cancelled</small><div class="h4 mb-0">{{ $counts['cancelled'] ?? 0 }}</div></div></div>
      </div>
    </div>
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Permintaan Terbaru</h5>
        <a href="{{ route('lab.requests.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm">
            <thead><tr><th>Tanggal</th><th>RM</th><th>Pasien</th><th>Status</th><th>Total</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
              @forelse($recent as $r)
                <tr>
                  <td>{{ $r->created_at->format('d M Y H:i') }}</td>
                  <td>{{ $r->encounter->rekam_medis ?? '-' }}</td>
                  <td>{{ $r->encounter->name_pasien ?? '-' }}</td>
                  <td><span class="badge bg-secondary">{{ ucfirst($r->status) }}</span></td>
                  <td>{{ 'Rp ' . number_format($r->total_charge,0,',','.') }}</td>
                  <td class="text-end">
                    <a href="{{ route('lab.requests.show', $r->id) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                    <a href="{{ route('lab.requests.edit', $r->id) }}" class="btn btn-sm btn-outline-primary">Hasil/Status</a>
                  </td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center text-muted">Belum ada data</td></tr>
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
