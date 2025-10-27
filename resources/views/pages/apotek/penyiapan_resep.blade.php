@extends('layouts.app')

@section('title', 'Penyiapan Resep Pasien')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* Hapus backdrop, buat modal dengan shadow saja */
        .modal-backdrop {
            display: none !important;
        }

        #modalDetail {
            background-color: rgba(0, 0, 0, 0.5) !important;
        }

        #modalDetail .modal-dialog {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.5) !important;
        }

        #modalDetail .modal-content {
            pointer-events: auto !important;
        }

        /* Pastikan SweetAlert muncul di atas modal */
        .swal2-container {
            z-index: 9999 !important;
        }
    </style>
@endpush

@section('content')
    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-xxl-12 col-sm-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Antrian Penyiapan Resep (Rawat Jalan & Resep Pulang)</h5>
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
                                        <th>Asal Resep</th>
                                        <th>Tanggal Resep</th>
                                        <th class="text-center">Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($reseps as $resep)
                                        @php
                                            // Cek apakah semua detail sudah disiapkan
                                            $allPrepared = !$resep->details->contains('status', 'Diajukan');
                                        @endphp
                                        <tr>
                                            <td class="text-center">
                                                {{ $loop->iteration + ($reseps->firstItem() ? $reseps->firstItem() - 1 : 0) }}
                                            </td>
                                            <td>{{ $resep->encounter?->rekam_medis ?? 'N/A' }}</td>
                                            <td>{{ $resep->encounter?->name_pasien ?? 'N/A' }}</td>
                                            <td>{{ $resep->encounter?->clinic?->name ?? 'Resep Pulang' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($resep->created_at)->format('d-m-Y H:i') }}</td>
                                            <td class="text-center status-badge">
                                                @if (!$allPrepared)
                                                    <span class="badge bg-warning">Menunggu Penyiapan</span>
                                                @else
                                                    <span class="badge bg-primary">{{ $resep->status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info btn-detail" data-id="{{ $resep->id }}"
                                                    data-pasien="{{ $resep->encounter?->name_pasien ?? 'Pasien' }}">
                                                    <i class="ri-file-list-2-line"></i> Detail
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada resep yang perlu disiapkan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- Paginate --}}
                    <div class="mt-3">
                        {{ $reseps->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Row ends -->

    <!-- Modal Detail Resep -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailLabel">Detail Resep</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalDetailBody">
                    <div class="text-center text-muted">Memuat data...</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-success d-none" id="btnSiapkanSemua">
                        <i class="ri-check-double-line"></i> Siapkan Semua
                    </button>
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
                const resepId = $(this).data('id');
                const namaPasien = $(this).data('pasien');
                const url = `{{ url('apotek/penyiapan-resep/detail') }}/${resepId}`;

                const modalDetailEl = document.getElementById('modalDetail');
                const modalDetailLabel = document.getElementById('modalDetailLabel');
                const modalDetailBody = document.getElementById('modalDetailBody');

                modalDetailLabel.textContent = 'Detail Resep untuk: ' + namaPasien;
                modalDetailBody.innerHTML = '<div class="text-center text-muted">Memuat data...</div>';

                // Tampilkan modal tanpa backdrop
                $(modalDetailEl).modal({
                    backdrop: false,
                    keyboard: true
                });
                $(modalDetailEl).modal('show');

                $.get(url, function(data) {
                    modalDetailBody.innerHTML = data;
                    if ($('#modalDetailBody').find('.btn-siapkan-item').length > 0) {
                        $('#btnSiapkanSemua').removeClass('d-none').data('id', resepId);
                    } else {
                        $('#btnSiapkanSemua').addClass('d-none');
                    }
                }).fail(function() {
                    modalDetailBody.innerHTML =
                        '<div class="text-center text-danger">Gagal memuat detail.</div>';
                });
            });

            // Cleanup saat modal ditutup
            $('#modalDetail').on('hidden.bs.modal', function() {
                // Hapus backdrop jika ada yang tertinggal
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css({
                    'overflow': '',
                    'padding-right': ''
                });
            });

            // Event delegation untuk tombol 'Siapkan' di dalam modal
            $('#modalDetailBody').on('click', '.btn-siapkan-item', function(e) {
                e.preventDefault();
                const resepDetailId = $(this).data('id');
                const url = `{{ url('apotek/penyiapan-resep/siapkan-item') }}/${resepDetailId}`;
                const button = $(this);

                Swal.fire({
                    title: 'Anda yakin?',
                    text: "Obat ini akan ditandai 'Disiapkan' dan stok akan dikurangi.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, siapkan item!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        button.prop('disabled', true).text('Memproses...');
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: response.message,
                                        icon: 'success'
                                    });
                                    // Update UI di dalam modal
                                    button.closest('td').html(
                                        '<span class="badge bg-success">Disiapkan</span>'
                                    );

                                    // Jika response menandakan semua item sudah siap
                                    if (response.all_prepared) {
                                        // Jika tidak ada, tampilkan notifikasi selesai dan muat ulang halaman
                                        Swal.fire({
                                            title: 'Selesai!',
                                            text: 'Semua item resep telah disiapkan dan tagihan telah dikirim ke kasir.',
                                            icon: 'success'
                                        }).then(() => location.reload());
                                    }
                                }
                            },
                            error: function(xhr) {
                                const err = xhr.responseJSON;
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: err ? err.message :
                                        'Terjadi kesalahan.',
                                    icon: 'error'
                                });
                                button.prop('disabled', false).html(
                                    '<i class="ri-check-line"></i> Siapkan');
                            }
                        });
                    }
                });
            });

            // Event untuk tombol "Siapkan Semua"
            $('#btnSiapkanSemua').on('click', function(e) {
                e.preventDefault();
                const resepId = $(this).data('id');
                const url = `{{ url('apotek/penyiapan-resep/siapkan') }}/${resepId}`;
                const button = $(this);

                Swal.fire({
                    title: 'Anda yakin?',
                    text: "Semua obat yang belum disiapkan akan diproses dan stok akan dikurangi.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, siapkan semua!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        button.prop('disabled', true).text('Memproses...');
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: response.message,
                                        icon: 'success'
                                    }).then(() => {
                                        $('#modalDetail').modal('hide');
                                        location.reload(); // Muat ulang halaman
                                    });
                                }
                            },
                            error: function(xhr) {
                                const err = xhr.responseJSON;
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: err ? err.message :
                                        'Terjadi kesalahan.',
                                    icon: 'error'
                                });
                                button.prop('disabled', false).html(
                                    '<i class="ri-check-double-line"></i> Siapkan Semua'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
