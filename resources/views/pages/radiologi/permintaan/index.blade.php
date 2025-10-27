@extends('layouts.app')

@section('title', 'Permintaan Radiologi')
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Permintaan Radiologi</h5>
                    <a href="{{ route('radiologi.requests.create') }}" class="btn btn-primary btn-sm">
                        <i class="ri-add-line"></i> Tambah Permintaan
                    </a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
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
                                @forelse(($requests ?? []) as $r)
                                    <tr>
                                        <td>{{ optional($r->pasien)->rekam_medis }}</td>
                                        <td>{{ optional($r->pasien)->name }}</td>
                                        <td>{{ optional($r->jenis)->name }}</td>
                                        <td>{{ $r->created_at?->format('d M Y H:i') }}</td>
                                        <td><span class="badge bg-secondary">{{ ucfirst($r->status) }}</span></td>
                                        <td>{{ optional($r->dokter)->name }}</td>
                                        <td>
                                            <div class="d-flex flex-wrap align-items-center gap-1">
                                                <a href="{{ route('radiologi.requests.show', $r->id) }}"
                                                    class="btn btn-outline-secondary btn-sm">Detail</a>
                                                @php($st = $r->status)
                                                @if ($st === 'requested' || $st === 'processing')
                                                    <a href="{{ route('radiologi.requests.results.edit', $r->id) }}"
                                                        class="btn btn-outline-success btn-sm">
                                                        <i class="ri-edit-line"></i> Isi Hasil
                                                    </a>
                                                @endif
                                                @if ($st === 'requested')
                                                    <form method="POST"
                                                        action="{{ route('radiologi.requests.status', $r->id) }}"
                                                        class="d-inline-block m-0">
                                                        @csrf
                                                        <input type="hidden" name="status" value="canceled">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                                            <i class="ri-close-line"></i> Batalkan
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Belum ada data permintaan
                                            radiologi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if (isset($requests) && method_exists($requests, 'links'))
                        <div class="mt-3">
                            {{ $requests->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Initialize DataTable when ready
        $(document).ready(function() {
            if ($.fn.DataTable) {
                $('#requestsTable').DataTable({
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
