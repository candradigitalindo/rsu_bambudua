@extends('layouts.app')
@section('title', 'Resepter Obat')
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <style>
        a.disabled {
            /* Make the disabled links grayish*/
            color: gray;
            /* And disable the pointer events */
            pointer-events: none;
        }
    </style>
@endpush
@section('content')
    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-xxl-12 col-sm-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Data Resep Dokter</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="card-info rounded-1 small lh-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="ms-auto d-flex gap-2">

                                <!-- Search Patient Starts -->
                                <div class="search-container d-xl-block d-none">
                                    <form method="GET" action="{{ route('apotek.getEncounter') }}">
                                        <input type="text" class="form-control" name="search" placeholder="Search"
                                            value="{{ request('search') }}">
                                        <i class="ri-search-line"></i>
                                    </form>
                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="table-outer">
                        <div class="table-responsive">
                            <table class="table truncate m-0">
                                <thead>
                                    <tr>
                                        <th>Nama Pasien</th>
                                        <th class="text-center">Tipe</th>
                                        <th class="text-center">Nama Dokter</th>
                                        <th class="text-center">Nominal</th>
                                        <th class="text-center">Diskon</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center" style="width: 20%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($encounters as $encounter)
                                        <tr>
                                            <td>{{ ucwords($encounter->name_pasien) }}</td>
                                            <td class="text-center">{{ $encounter->type_label }}</td>
                                            <td class="text-center">
                                                @if ($encounter->practitioner)
                                                    @if ($encounter->practitioner instanceof \Illuminate\Support\Collection)
                                                        {{ $encounter->practitioner->pluck('user.name')->join(', ') ?: '-' }}
                                                    @elseif($encounter->practitioner->user && $encounter->practitioner->user->role == 2)
                                                        {{ $encounter->practitioner->user->name }}
                                                    @else
                                                        -
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center"><span
                                                    class="text-primary fw-bold">{{ formatPrice($encounter->total_resep) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="text-danger fw-bold">{{ formatPrice($encounter->diskon_resep) }}
                                                    ({{ $encounter->diskon_persen_resep }}%)
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="text-success fw-bold">{{ formatPrice($encounter->total_bayar_resep) }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if ($encounter->status_bayar_resep == 0)
                                                    <span class="badge bg-warning">Belum Dibayar</span>
                                                @elseif ($encounter->status_bayar_resep == 1)
                                                    <span class="badge bg-success">Sudah Dibayar</span>
                                                @else
                                                    <span class="badge bg-danger">Batal</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-info btn-detail-resep"
                                                        data-id="{{ $encounter->id }}"
                                                        data-nama="{{ $encounter->name_pasien }}">
                                                        <i class="ri-file-list-2-line"></i> Detail
                                                    </button>

                                                </div>
                                                {{-- button bayar --}}
                                                <div class="btn-group">
                                                    @if ($encounter->status_bayar_resep == 0)
                                                        <button type="button"
                                                            class="btn btn-sm btn-success btn-bayar-resep"
                                                            data-id="{{ $encounter->id }}"
                                                            data-nama="{{ $encounter->name_pasien }}">
                                                            <i class="ri-money-dollar-circle-line"></i> Bayar
                                                        </button>
                                                    @else
                                                        {{-- Cetak Struk --}}
                                                        <button type="button"
                                                            class="btn btn-sm btn-primary btn-cetak-struk"
                                                            data-id="{{ $encounter->id }}"
                                                            data-nama="{{ $encounter->name_pasien }}">
                                                            <i class="ri-printer-line"></i> Cetak Struk
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Data tidak ada</td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- Paginate --}}
                    <div class="mt-3">
                        {{ $encounters->links() }}
                    </div>

                </div>
            </div>
        </div>

    </div>
    <!-- Row ends -->

    <!-- Modal Dinamis -->
    <div class="modal fade" id="modalResepDetail" tabindex="-1" aria-labelledby="modalResepDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalResepDetailLabel">Detail Resep Pasien</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalResepDetailBody">
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
    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <!-- SweetAlert2 JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).on('click', '.btn-detail-resep', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            $('#modalResepDetailLabel').text('Detail Resep Pasien: ' + nama);
            $('#modalResepDetailBody').html('<div class="text-center text-muted">Memuat data...</div>');
            $('#modalResepDetail').modal('show');
            $.get("{{ url('apotek/resep-detail') }}/" + id, function(res) {
                $('#modalResepDetailBody').html(res);
            }).fail(function() {
                $('#modalResepDetailBody').html(
                    '<div class="text-danger text-center">Gagal memuat data.</div>');
            });
        });
        $(document).on('click', '.btn-bayar-resep', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            Swal.fire({
                title: 'Konfirmasi Pembayaran',
                text: 'Pilih metode pembayaran untuk ' + nama,
                icon: 'question',
                input: 'select',
                inputOptions: {
                    'Tunai': 'Tunai',
                    'Debit': 'Debit',
                    'QRIS': 'QRIS',
                    'Transfer': 'Transfer'
                },
                inputPlaceholder: 'Pilih metode pembayaran',
                showCancelButton: true,
                confirmButtonText: 'Bayar',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Silakan pilih metode pembayaran!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("{{ url('apotek/encounter') }}/" + id + "/bayar", {
                        _token: '{{ csrf_token() }}',
                        metode_pembayaran: result.value
                    }, function(res) {
                        if (res.status == 'success') {
                            Swal.fire({
                                title: 'Berhasil',
                                text: res.message,
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal',
                                text: res.message,
                                icon: 'error'
                            });
                        }
                    }).fail(function() {
                        Swal.fire({
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat memproses pembayaran.',
                            icon: 'error'
                        });
                    });
                }
            });
        });
        $(document).on('click', '.btn-cetak-struk', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            Swal.fire({
                title: 'Konfirmasi Cetak Struk',
                text: 'Apakah Anda yakin ingin mencetak struk untuk ' + nama + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Cetak',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open("{{ url('apotek/encounter') }}/" + id + "/cetak", '_blank');
                }
            });
        });
    </script>
@endpush
