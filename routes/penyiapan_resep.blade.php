@extends('layouts.app')

@section('title', 'Penyiapan Resep Pasien')

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
                                    {{-- @forelse ($reseps as $resep) --}}
                                    <tr>
                                        {{-- Contoh Data --}}
                                        <td class="text-center">1</td>
                                        <td>000001</td>
                                        <td>John Doe</td>
                                        <td>Poliklinik Umum</td>
                                        <td>{{ now()->format('d-m-Y H:i') }}</td>
                                        <td class="text-center"><span class="badge bg-warning">Menunggu</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-info">
                                                <i class="ri-file-list-2-line"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                    {{-- @empty --}}
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada resep yang perlu disiapkan.
                                        </td>
                                    </tr>
                                    {{-- @endforelse --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- Paginate --}}
                    <div class="mt-3">
                        {{-- {{ $reseps->links() }} --}}
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Row ends -->

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
                var resepId = $(this).data('id');
                var namaPasien = $(this).data('pasien');
                var url = "{{ url('apotek/penyiapan-resep/detail') }}/" + resepId;

                $('#modalDetailLabel').text('Detail Resep untuk: ' + namaPasien);
                $('#modalDetailBody').html('<div class="text-center text-muted">Memuat data...</div>');
                $('#modalDetail').modal('show');

                $.get(url, function(data) {
                    $('#modalDetailBody').html(data);
                }).fail(function() {
                    $('#modalDetailBody').html(
                        '<div class="text-center text-danger">Gagal memuat detail.</div>');
                });
            });

            // Event delegation untuk tombol 'Siapkan' di dalam modal
            $('#modalDetailBody').on('click', '.btn-siapkan-resep', function(e) {
                e.preventDefault();
                var resepId = $(this).data('id');
                var url = "{{ url('apotek/penyiapan-resep/siapkan') }}/" + resepId;
                var button = $(this);

                Swal.fire({
                    title: 'Anda yakin?',
                    text: "Semua obat akan ditandai 'Disiapkan' dan stok akan dikurangi.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, siapkan resep!',
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
                                    Swal.fire(
                                        'Berhasil!',
                                        response.message,
                                        'success'
                                    ).then(() => {
                                        location
                                    .reload(); // Reload halaman untuk update tabel utama
                                    });
                                    $('#modalDetail').modal('hide');
                                }
                            },
                            error: function(xhr) {
                                var err = xhr.responseJSON;
                                Swal.fire(
                                    'Gagal!',
                                    err.message,
                                    'error'
                                );
                                button.prop('disabled', false).html(
                                    '<i class="ri-check-double-line"></i> Tandai Semua Sebagai "Disiapkan"'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
