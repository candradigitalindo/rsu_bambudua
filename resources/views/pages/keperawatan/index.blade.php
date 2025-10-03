@extends('layouts.app')
@section('title','Asuhan Keperawatan')
@push('style')
<link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-12">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Asuhan Keperawatan</h5>
        <a href="{{ route('keperawatan.create') }}" class="btn btn-primary btn-sm"><i class="ri-add-line"></i> Catat Asuhan</a>
      </div>
      <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
          <div class="col-md-4">
            <input type="text" name="q" class="form-control" placeholder="Cari RM/Nama pasien..." value="{{ $q ?? '' }}">
          </div>
          <div class="col-md-2">
            <button class="btn btn-outline-primary" type="submit">Cari</button>
          </div>
        </form>
        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>RM</th>
                <th>Pasien</th>
                <th>Perawat</th>
                <th>Shift</th>
                <th class="text-end">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($records as $r)
              <tr>
                <td>{{ $r->created_at->format('d M Y H:i') }}</td>
                <td>{{ $r->encounter->rekam_medis ?? '-' }}</td>
                <td>{{ $r->encounter->name_pasien ?? '-' }}</td>
                <td>{{ $r->nurse->name ?? '-' }}</td>
                <td>{{ $r->shift ?? '-' }}</td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-secondary" href="{{ route('keperawatan.show', $r->id) }}">Detail</a>
                  <a class="btn btn-sm btn-outline-primary" href="{{ route('keperawatan.edit', $r->id) }}">Edit</a>
                  <a class="btn btn-sm btn-outline-info" href="{{ route('keperawatan.print', $r->id) }}" target="_blank">Cetak</a>
                </td>
              </tr>
              @empty
              <tr><td colspan="6" class="text-center text-muted">Belum ada data</td></tr>
              @endforelse
            </tbody>
          </table>
          {{ $records->onEachSide(1)->links() }}
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
