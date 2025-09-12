@extends('layouts.app')
@section('title', 'Data Tindakan Dokter')
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
                    <h5 class="card-title">Tabel Tindakan Dokter</h5>
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
                                    <form method="GET" action="{{ route('loket.getEncounter') }}">
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
                                                    class="text-primary fw-bold">{{ formatPrice($encounter->total_tindakan) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="text-success fw-bold">{{ formatPrice($encounter->total_bayar_tindakan) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info">Selesai</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-info btn-detail-tindakan"
                                                        data-id="{{ $encounter->id }}"
                                                        data-nama="{{ $encounter->name_pasien }}">
                                                        <i class="ri-file-list-2-line"></i> Detail
                                                    </button>

                                                </div>
                                                {{-- button bayar --}}
                                                <div class="btn-group">
                                                    {{-- Cetak Struk --}}
                                                    <button type="button" class="btn btn-sm btn-primary btn-cetak-struk"
                                                        data-id="{{ $encounter->id }}"
                                                        data-nama="{{ $encounter->name_pasien }}">
                                                        <i class="ri-printer-line"></i> Cetak Struk
                                                    </button>
                                                </div>
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Data tidak ada</td>
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
    <div class="modal fade" id="modalTindakanDetail" tabindex="-1" aria-labelledby="modalTindakanDetailLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTindakanDetailLabel">Detail Tindakan Pasien</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalTindakanDetailBody">
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
        $(document).on('click', '.btn-detail-tindakan', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            $('#modalTindakanDetailLabel').text('Detail Tindakan Pasien: ' + nama);
            $('#modalTindakanDetailBody').html('<div class="text-center text-muted">Memuat data...</div>');
            $('#modalTindakanDetail').modal('show');
            $.get("{{ url('loket/tindakan-detail') }}/" + id, function(res) {
                $('#modalTindakanDetailBody').html(res);
            }).fail(function() {
                $('#modalTindakanDetailBody').html(
                    '<div class="text-danger text-center">Gagal memuat data.</div>');
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
                    window.open("{{ url('loket/encounter') }}/" + id + "/cetak", '_blank');
                }
            });
        });
    </script>
@endpush
