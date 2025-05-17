@extends('layouts.app')
@section('title')
    Data Bahan
@endsection
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
                    <h5 class="card-title">Data Bahan</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="card-info rounded-1 small lh-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="ms-auto d-flex gap-2">

                                <!-- Search Patient Starts -->
                                <div class="search-container d-xl-block d-none">
                                    <form method="GET" action="{{ route('bahans.index') }}">
                                        <input type="text" class="form-control" name="name" placeholder="Search">
                                        <i class="ri-search-line"></i>
                                    </form>
                                </div>
                                <!-- Search Patient Ends -->
                                <div class="ms-2">
                                    <a href="{{ route('bahan.getRequestBahan') }}" class="btn btn-outline-primary" id="permintaanTindakan">
                                        <i class="ri-calendar-todo-fill"></i>
                                        <span class="btn-text" id="textPermintaanTindakan">Permintaan Tindakan</span>
                                        <span class="spinner-border spinner-border-sm d-none"
                                            id="spinerPermintaanTindakan"></span>
                                    </a>
                                </div>
                                <div class="ms-2">
                                    <a href="{{ route('bahan.getAllHistori') }}" class="btn btn-outline-primary"
                                        id="histori">
                                        <i class="ri-calendar-todo-fill"></i>
                                        <span class="btn-text" id="textHistori">Histori</span>
                                        <span class="spinner-border spinner-border-sm d-none" id="spinerHistori"></span>
                                    </a>
                                </div>
                                <!-- Button Group Starts -->
                                <div class="ms-2">
                                    <a href="{{ route('bahans.create') }}" class="btn btn-outline-primary" id="createBahan">
                                        <i class="ri-calendar-todo-fill"></i>
                                        <span class="btn-text" id="textCreateBahan">Tambah Data Baru</span>
                                        <span class="spinner-border spinner-border-sm d-none" id="spinerCreateBahan"></span>
                                    </a>
                                </div>
                                <!-- Button Group Ends -->

                            </div>
                        </div>

                    </div>

                    <div class="table-outer">
                        <div class="table-responsive">
                            <table class="table truncate m-0">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th class="text-center">Memiliki Expired</th>
                                        <th class="text-center">Jumlah Stok</th>
                                        <th class="text-center">Warning Expired</th>
                                        <th class="text-center">Jumlah Expired</th>
                                        <th class="text-center">Dipakai Tindakan</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center" style="width: 20%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($bahans as $bahan)
                                        <tr>
                                            <td>{{ ucwords($bahan->name) }}</td>
                                            <td class="text-center">{{ $bahan->is_expired == 1 ? 'YA' : 'TIDAK' }}</td>
                                            <td class="text-center"><span
                                                    class="text-primary fw-bold">{{ $bahan->getStockQuantityAttribute() }}</span>
                                            </td>
                                            <td class="text-center">

                                                @if ($bahan->is_expired == 1)
                                                    @if ($bahan->getWarningStockQuantityAttribute() > 0)
                                                        <span
                                                            class="text-info fw-bold">{{ $bahan->getWarningStockQuantityAttribute() }}</span>
                                                    @else
                                                        <span class="text-success">Tidak ada</span>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($bahan->is_expired == 1)
                                                    @if ($bahan->getExpiredStockQuantityAttribute() > 0)
                                                        <span
                                                            class="text-danger fw-bold">{{ $bahan->getExpiredStockQuantityAttribute() }}</span>
                                                    @else
                                                        <span class="text-success">Tidak ada</span>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">

                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                     data-bs-toggle="modal"
                                                    data-bs-target="#modalDetailTindakan-{{ $bahan->id }}">
                                                    <i class="ri-archive-line"></i>
                                                    <span class="btn-text"
                                                        id="textHistori-{{ $bahan->id }}">Detail Tindakan</span>
                                                    <span class="spinner-border spinner-border-sm d-none"
                                                        id="spinerHistori-{{ $bahan->id }}"></span>
                                                </button>
                                                <div class="modal fade" id="modalDetailTindakan-{{ $bahan->id }}"
                                                    tabindex="-1" role="dialog"
                                                    aria-labelledby="modalDetailTindakanLabel-{{ $bahan->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="modalDetailTindakanLabel-{{ $bahan->id }}">
                                                                    Tindakan yang menggunakan {{ $bahan->name }}</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Nama Tindakan</th>
                                                                            <th>Harga</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($bahan->tindakan as $tindakan)
                                                                            <tr>
                                                                                <td>{{ $tindakan->name }}</td>
                                                                                <td>{{ $tindakan->getHargaFormattedAttribute() }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if ($bahan->is_active == 1)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-danger">Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td class="text-center">

                                                <a href="{{ route('bahan.getBahan', $bahan->id) }}"
                                                    class="btn btn-outline-primary btn-sm"
                                                    id="stokMasuk-{{ $bahan->id }}">
                                                    <i class="ri-calendar-todo-fill"></i>
                                                    <span class="btn-text" id="textStokMasuk-{{ $bahan->id }}">Stok
                                                        Masuk</span>
                                                    <span class="spinner-border spinner-border-sm d-none"
                                                        id="spinerStokMasuk-{{ $bahan->id }}"></span>
                                                </a>
                                                <a href="{{ route('bahan.getBahanKeluar', $bahan->id) }}"
                                                    class="btn btn-outline-danger btn-sm"
                                                    id="stokKeluar-{{ $bahan->id }}">
                                                    <i class="ri-calendar-todo-fill"></i>
                                                    <span class="btn-text" id="textStokKeluar-{{ $bahan->id }}">Stok
                                                        Keluar</span>
                                                    <span class="spinner-border spinner-border-sm d-none"
                                                        id="spinerStokKeluar-{{ $bahan->id }}"></span>
                                                </a>
                                                <a href="{{ route('bahans.edit', $bahan->id) }}"
                                                    class="btn btn-primary btn-sm" id="edit-{{ $bahan->id }}">
                                                    <i class="ri-edit-2-line"></i>
                                                    <span class="btn-text" id="text-{{ $bahan->id }}">Edit </span>
                                                    <span class="spinner-border spinner-border-sm d-none"
                                                        id="spiner-{{ $bahan->id }}"></span>
                                                </a>
                                                <form action="{{ route('bahans.destroy', $bahan->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Apakah anda yakin ingin menghapus Item {{ $bahan->name }} ?')">
                                                        <i class="ri-delete-bin-5-line"></i>
                                                        Hapus
                                                    </button>
                                                </form>
                                                <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                                                <script src="{{ asset('js/jquery.min.js') }}"></script>

                                                <script>
                                                    $(document).ready(function() {
                                                        $("#edit-{{ $bahan->id }}").click(function() {
                                                            $("#spiner-{{ $bahan->id }}").removeClass("d-none");
                                                            $("#edit-{{ $bahan->id }}").addClass("disabled", true);
                                                            $("#text-{{ $bahan->id }}").text("Mohon Tunggu ...");
                                                        });
                                                        $("#histori-{{ $bahan->id }}").click(function() {
                                                            $("#spinerHistori-{{ $bahan->id }}").removeClass("d-none");
                                                            $("#histori-{{ $bahan->id }}").addClass("disabled", true);
                                                            $("#textHistori-{{ $bahan->id }}").text("Mohon Tunggu ...");
                                                        });

                                                        $("#stokKeluar-{{ $bahan->id }}").click(function() {
                                                            $("#spinerStokKeluar-{{ $bahan->id }}").removeClass("d-none");
                                                            $("#stokKeluar-{{ $bahan->id }}").addClass("disabled", true);
                                                            $("#textStokKeluar-{{ $bahan->id }}").text("Mohon Tunggu ...");
                                                        });
                                                        $("#stokMasuk-{{ $bahan->id }}").click(function() {
                                                            $("#spinerStokMasuk-{{ $bahan->id }}").removeClass("d-none");
                                                            $("#stokMasuk-{{ $bahan->id }}").addClass("disabled", true);
                                                            $("#textStokMasuk-{{ $bahan->id }}").text("Mohon Tunggu ...");
                                                        })

                                                    });
                                                </script>

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Data tidak ada</td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
    <!-- Row ends -->
@endsection
@push('scripts')
    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#createBahan").click(function() {
                $("#spinerCreateBahan").removeClass("d-none");
                $("#createBahan").addClass("disabled", true);
                $("#textCreateBahan").text("Mohon Tunggu ...");
            });
            $("#histori").click(function() {
                $("#spinerHistori").removeClass("d-none");
                $("#histori").addClass("disabled", true);
                $("#textHistori").text("Mohon Tunggu ...");
            });

        });
    </script>
@endpush
