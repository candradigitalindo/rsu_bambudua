@extends('layouts.app')
@section('title','Permintaan Laboratorium')
@push('style')
<link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-12">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Permintaan Laboratorium</h5>
        <a href="{{ route('lab.requests.create') }}" class="btn btn-primary btn-sm"><i class="ri-add-line"></i> Buat Permintaan</a>
      </div>
      <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
          <div class="col-md-4">
            <input type="text" name="q" class="form-control" placeholder="Cari RM/Nama pasien..." value="{{ $q ?? '' }}">
          </div>
          <div class="col-md-3">
            <select name="status" class="form-select">
              <option value="">Semua Status</option>
              @foreach(['requested','collected','processing','completed','cancelled'] as $st)
                <option value="{{ $st }}" {{ ($status ?? '')===$st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
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
                <th>RM</th>
                <th>Pasien</th>
                <th>Status</th>
                <th>Total</th>
                <th class="text-end">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($requests as $r)
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
          {{ $requests->onEachSide(1)->links() }}
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
