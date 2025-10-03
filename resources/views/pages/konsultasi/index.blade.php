@extends('layouts.app')
@section('title', 'Konsultasi Spesialis')
@push('style')
<link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-12">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Konsultasi Spesialis</h5>
        <div>
          <a href="{{ route('konsultasi.create') }}" class="btn btn-primary btn-sm"><i class="ri-add-line"></i> Buat Permintaan</a>
        </div>
      </div>
      <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
          <div class="col-md-4">
            <input type="text" class="form-control" name="q" value="{{ $q }}" placeholder="Cari RM/Nama pasien...">
          </div>
          <div class="col-md-3">
            <select class="form-select" name="status">
              <option value="">Semua Status</option>
              @foreach(['requested'=>'Requested','scheduled'=>'Scheduled','completed'=>'Completed','cancelled'=>'Cancelled'] as $k=>$v)
                <option value="{{ $k }}" {{ ($status ?? '')===$k ? 'selected':'' }}>{{ $v }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <button class="btn btn-outline-primary" type="submit">Filter</button>
          </div>
        </form>
        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Rekam Medis</th>
                <th>Nama Pasien</th>
                <th>Spesialis</th>
                <th>Jadwal</th>
                <th>Status</th>
                <th class="text-end">Aksi</th>
              </tr>
            </thead>
            <tbody>
            @forelse($consultations as $c)
              <tr>
                <td>{{ $c->created_at->format('d M Y H:i') }}</td>
                <td>{{ $c->encounter->rekam_medis ?? '-' }}</td>
                <td>{{ $c->encounter->name_pasien ?? '-' }}</td>
                <td>{{ $c->specialist->name ?? '-' }}</td>
                <td>{{ $c->scheduled_at ? $c->scheduled_at->format('d M Y H:i') : '-' }}</td>
                <td><span class="badge bg-secondary">{{ ucfirst($c->status) }}</span></td>
                <td class="text-end">
                  <a href="{{ route('konsultasi.show', $c->id) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                  <a href="{{ route('konsultasi.edit', $c->id) }}" class="btn btn-sm btn-outline-primary">Kelola</a>
                  <a href="{{ route('konsultasi.print', $c->id) }}" target="_blank" class="btn btn-sm btn-outline-info">Cetak</a>
                </td>
              </tr>
            @empty
              <tr><td colspan="7" class="text-center text-muted">Belum ada data</td></tr>
            @endforelse
            </tbody>
          </table>
          {{ $consultations->onEachSide(1)->links() }}
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
