@extends('layouts.app')

@section('title', 'Jadwal Radiologi')
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Jadwal Radiologi</h5>
      </div>
      <div class="card-body">
        <p class="text-muted">Halaman jadwal untuk penjadwalan pemeriksaan radiologi.</p>
      <div class="card-body">
        <form class="row g-2 mb-3" method="GET" action="{{ route('radiologi.schedule.index') }}">
          <div class="col-auto">
            <input type="date" class="form-control" name="date" value="{{ $date ?? now()->toDateString() }}">
          </div>
          <div class="col-auto">
            <button class="btn btn-outline-primary" type="submit">Filter</button>
          </div>
        </form>
        <div class="table-responsive">
          <table class="table table-hover" id="scheduleTable">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Pasien</th>
                <th>Pemeriksaan</th>
                <th>Modality/Ruang</th>
                <th>Radiografer</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse(($schedules ?? []) as $s)
              <tr>
                <td>{{ $s->scheduled_start?->format('d M Y') }}</td>
                <td>{{ $s->scheduled_start?->format('H:i') }}{{ $s->scheduled_end ? ' - '.$s->scheduled_end->format('H:i') : '' }}</td>
                <td>{{ optional($s->request?->pasien)->rekam_medis }} — {{ optional($s->request?->pasien)->name }}</td>
                <td>{{ optional($s->request?->jenis)->name }}</td>
                <td>{{ $s->modality ?? '-' }} / {{ $s->room ?? '-' }}</td>
                <td>{{ optional($s->radiographer)->name ?? '-' }}</td>
                <td><span class="badge bg-secondary">{{ str_replace('_',' ', ucfirst($s->status)) }}</span></td>
                <td>
                  <div class="btn-group btn-group-sm" role="group">
                    @if($s->status === 'scheduled')
                      <form method="POST" action="{{ route('radiologi.schedule.start', $s->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-success">Mulai</button>
                      </form>
                      <form method="POST" action="{{ route('radiologi.schedule.cancel', $s->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">Batalkan</button>
                      </form>
                      <form method="POST" action="{{ route('radiologi.schedule.no_show', $s->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-warning">No-show</button>
                      </form>
                    @elseif($s->status === 'in_progress')
                      <a href="{{ route('radiologi.requests.results.edit', $s->radiology_request_id) }}" class="btn btn-outline-primary">Input Hasil</a>
                    @endif
                    <a href="{{ route('radiologi.requests.show', $s->radiology_request_id) }}" class="btn btn-outline-secondary">Detail</a>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="8" class="text-center text-muted">Belum ada jadwal.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if(isset($schedules) && method_exists($schedules, 'links'))
          <div class="mt-3">{{ $schedules->links() }}</div>
        @endif
      </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    if ($.fn.DataTable) {
        $('#scheduleTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
            }
        });
    }
});
</script>
@endpush

@push('scripts')
<script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
<script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>
@endpush
