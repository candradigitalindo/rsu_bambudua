@extends('layouts.app')

@section('title', 'Hasil Radiologi')
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Hasil Radiologi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="resultsTable">
                            <thead>
                                <tr>
                                    <th>Tanggal Hasil</th>
                                    <th>No. RM</th>
                                    <th>Nama Pasien</th>
                                    <th>Pemeriksaan</th>
                                    <th>Dokter Perujuk</th>
                                    <th>Dokter Pelapor</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($results as $result)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($result->reported_at)->format('d M Y H:i') }}</td>
                                        <td>{{ $result->request->pasien->rekam_medis ?? '-' }}</td>
                                        <td>{{ $result->request->pasien->name ?? '-' }}</td>
                                        <td>{{ $result->request->jenis->name ?? '-' }}</td>
                                        <td>{{ $result->request->dokter->name ?? '-' }}</td>
                                        <td>{{ $result->reporter->name ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('radiologi.requests.show', $result->request->id) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Belum ada data hasil radiologi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        @if ($results instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            {{ $results->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Hapus inisialisasi DataTable agar paginasi Laravel berfungsi
            // if ($.fn.DataTable) {
            //     $('#resultsTable').DataTable({
            //         language: {
            //             url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
            //         },
            //         paging: false, // Matikan paging dari datatables
            //     });
            // }
        });
    </script>
@endpush

@push('scripts')
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
@endpush
