@extends('layouts.app')
@section('title')
    Pendapatan Lainnya
@endsection
@push('style')
    <!-- Data Tables -->
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bs5.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bs5-custom.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/buttons/dataTables.bs5-custom.css') }}">
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Data Pendapatan Lainnya</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <a href="{{ route('pendapatan-lain.create') }}" class="btn btn-primary">
                            <i class="ri-add-line"></i> Tambah Pendapatan
                        </a>
                        <form action="{{ route('pendapatan-lain.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                            <div class="d-flex align-items-center">
                                <label for="start_date" class="form-label me-2 mb-0">Dari</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                    value="{{ $startDate }}">
                            </div>
                            <div class="d-flex align-items-center">
                                <label for="end_date" class="form-label me-2 mb-0">Sampai</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                    value="{{ $endDate }}">
                            </div>
                            <button type="submit" class="btn btn-info">
                                <i class="ri-filter-3-line"></i> Filter
                            </button>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table id="datatable-button" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Deskripsi</th>
                                    <th class="text-end">Jumlah (Rp)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($incomes as $income)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ \Carbon\Carbon::parse($income->income_date)->translatedFormat('d F Y') }}
                                        </td>
                                        <td>{{ $income->description }}</td>
                                        <td class="text-end">{{ formatPrice($income->amount) }}</td>
                                        <td>
                                            <div class="d-inline-flex gap-1">
                                                <a href="{{ route('pendapatan-lain.edit', $income->id) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="ri-edit-line"></i>
                                                </a>
                                                <form action="{{ route('pendapatan-lain.destroy', $income->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm btn-delete">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data pendapatan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Data Tables -->
    <script src="{{ asset('vendor/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/buttons.print.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/jszip.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/pdfmake.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/vfs_fonts.js') }}"></script>
    <script src="{{ asset('vendor/datatables/custom/custom-datatables.js') }}"></script>
    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        // SweetAlert untuk konfirmasi hapus
        $('.btn-delete').on('click', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            Swal.fire({
                title: 'Hapus Data!',
                text: "Apakah Anda yakin ingin menghapus data ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
@endpush
