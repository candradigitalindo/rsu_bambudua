@extends('layouts.app')

@section('title', 'Permintaan Obat Rawat Inap')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-xxl-12 col-sm-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Permintaan Obat Rawat Inap</h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-3" id="filterForm">
                        <div class="d-flex flex-wrap gap-2">
                            <div>
                                <label for="start_date" class="form-label mb-0">Start Date</label>
                                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                                    class="form-control form-control-sm">
                            </div>
                            <div>
                                <label for="end_date" class="form-label mb-0">End Date</label>
                                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="align-self-end">
                                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>

                    <hr>

                    <div class="table-outer">
                        <div class="table-responsive">
                            <table class="table truncate m-0">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>No. RM</th>
                                        <th>Nama Pasien</th>
                                        <th>Tanggal Permintaan</th>
                                        <th class="text-center">Item Diajukan</th>
                                        <th class="text-center">Item Disiapkan</th>
                                        <th class="text-center">Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($permintaan as $admissionId => $items)
                                        @php
                                            $firstItem = $items->first();
                                            $diajukanCount = $items->where('status', 'Diajukan')->count();
                                            $disiapkanCount = $items->where('status', 'Disiapkan')->count();
                                            $diberikanCount = $items->where('status', 'Diberikan')->count();

                                            $statusBadge = '<span class="badge bg-secondary">Selesai</span>';
                                            if ($diajukanCount > 0) {
                                                $statusBadge = '<span class="badge bg-warning">Menunggu</span>';
                                            } elseif ($disiapkanCount > 0) {
                                                $statusBadge = '<span class="badge bg-info">Siap Bayar</span>';
                                            }
                                        @endphp
                                        <tr>
                                            <td class="text-center">
                                                {{ $loop->iteration + $permintaan->firstItem() - 1 }}
                                            </td>
                                            <td>{{ $firstItem->admission?->encounter?->rekam_medis ?? 'N/A' }}</td>
                                            <td>{{ $firstItem->admission?->encounter?->name_pasien ?? 'N/A' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($firstItem->created_at)->format('d-m-Y H:i') }}
                                            </td>
                                            <td class="text-center"><span
                                                    class="badge bg-warning">{{ $diajukanCount }}</span></td>
                                            <td class="text-center"><span class="badge bg-info">{{ $disiapkanCount }}</span>
                                            </td>
                                            <td class="text-center">
                                                {!! $statusBadge !!}
                                            </td>
                                            <td id="actions-{{ $admissionId }}">
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-info btn-detail"
                                                        data-id="{{ $admissionId }}"
                                                        data-pasien="{{ $firstItem->admission?->encounter?->name_pasien ?? 'Pasien' }}">
                                                        <i class="ri-file-list-2-line"></i> Detail
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Tidak ada permintaan obat yang perlu
                                                disiapkan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- Paginate --}}
                    <div class="mt-3">
                        {{ $permintaan->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Row ends -->

    <!-- Modal Detail Permintaan -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailLabel">Detail Permintaan Obat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalDetailBody">
                    <div class="text-center text-muted">Memuat data...</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>

    <!-- Page Specific JS File -->
    <script>
        $(document).ready(function() {
            // Handle klik tombol detail
            $('.btn-detail').on('click', function() {
                var permintaanId = $(this).data('id');
                var namaPasien = $(this).data('pasien');
                var url = "{{ url('apotek/permintaan-inap/detail-grouped') }}/" + permintaanId;

                $('#modalDetailLabel').text('Detail Permintaan Obat untuk: ' + namaPasien);
                $('#modalDetailBody').html(
                    '<div class="text-center text-muted">Memuat data...</div>');
                $('#modalDetail').modal('show');

                $.get(url, function(data) {
                    $('#modalDetailBody').html(data);
                }).fail(function() {
                    $('#modalDetailBody').html(
                        '<div class="text-center text-danger">Gagal memuat detail.</div>');
                });
            });

            // Event delegation untuk tombol 'Siapkan' di dalam modal
            $('#modalDetailBody').on('click', '.btn-siapkan', function(e) {
                e.preventDefault();
                var permintaanId = $(this).data('id');
                var url = "{{ url('apotek/permintaan-inap/siapkan') }}/" + permintaanId;
                var button = $(this);

                Swal.fire({
                    title: 'Anda yakin?',
                    text: "Obat akan ditandai sebagai 'Disiapkan' dan tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, siapkan obat!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire(
                                        'Berhasil!',
                                        response.message,
                                        'success'
                                    );
                                    // Update UI di dalam modal
                                    button.closest('tr').find('.status-badge')
                                        .removeClass('bg-warning').addClass('bg-info')
                                        .text('Disiapkan');
                                    button.replaceWith('-');
                                }
                            },
                            error: function(xhr) {
                                var err = JSON.parse(xhr.responseText);
                                Swal.fire(
                                    'Gagal!',
                                    err.message,
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

        });
    </script>
@endpush
