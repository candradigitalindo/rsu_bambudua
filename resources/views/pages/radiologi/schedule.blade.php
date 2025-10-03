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
                    <div class="table-responsive">
                        <table class="table table-hover" id="scheduleTable">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Pasien</th>
                                    <th>Pemeriksaan</th>
                                    <th>Dokter</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada jadwal.</td>
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
        $(document).ready(function() {
            $('#scheduleTable').DataTable({
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
