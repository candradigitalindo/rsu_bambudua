@extends('layouts.app')

@section('title', 'Permintaan Radiologi')
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Permintaan Radiologi</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="requestsTable">
            <thead>
              <tr>
                <th>No. RM</th>
                <th>Nama Pasien</th>
                <th>Pemeriksaan</th>
                <th>Tgl Permintaan</th>
                <th>Status</th>
                <th>Dokter Pengirim</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="7" class="text-center text-muted">Belum ada data permintaan radiologi.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Initialize DataTable when ready
$(document).ready(function() {
    $('#requestsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
        }
    });
});
</script>
@endpush

@push('scripts')
<script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
<script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>
@endpush
